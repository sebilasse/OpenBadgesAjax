<?php session_start();

//Badge-It AJAX v0.5.0 - Simple scripted system to award and issue badges into Mozilla Open Badges Infrastructure
//Copyright (c) 2012 Sebastian Lasse New Media - sebastianlasse.de
//inspired by Badge-It Gadget Lite
//Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

include 'settings.php';

// CSRF - force checks:
if(empty($_POST['a'])){ die('0000'); }
if(empty($_POST['type'])){ die('0'); }
if(!isset($_POST['_A'])){ die('1'); }
if(!isset($_POST['_B'])){ die('2'); }
$cookiename = str_replace(".","_",$issuer_host).'_openbadges';
if($_POST['_A']!=$_SESSION[$_COOKIE[$cookiename]]){ die('3'); }
if($_POST['_B']!=openssl_digest($_SERVER['REMOTE_ADDR'], 'sha512')){ die('4'); }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
header('Content-Type: application/json; charset=UTF-8');
/* BrowserID class */
class BrowserID {
	private $audience;
	private $assertion;
	private $email;
	private $validity;
	private $issuer;
	
	private function post_request($url, $data){
		$params = array('http' => array('method' => 'POST', 'content' => $data));
		$ctx = stream_context_create($params);
		$fp = fopen($url, 'rb', false, $ctx);
		if ($fp) {
		  return stream_get_contents($fp);
		} else {
		  return FALSE;
		}
	}
	
	public function BrowserID($audience, $assertion) {
		$this->audience = $audience;
		$this->assertion = $assertion;
	}
	
	/*
	* This response is read to determine is the assertion is authentic ...
	*/
	public function verify_assertion() {
		$parameters = http_build_query(array('assertion' => $this->assertion, 'audience' => $this->audience));
		$result = json_decode($this->post_request('https://browserid.org/verify', $parameters), TRUE, 2);
		if(isset($result['status']) && $result['status'] == 'okay') {
		  $this->email = $result['email'];
		  $this->validity = $result['valid-until'];
		  $this->issuer = $result['issuer'];
		  return true;
		} else {
		  return false;
		}
	}
	
	public function get_email() {
		return $this->email;
	}
	
	public function get_validity() {
		return $this->validity;
	}
	
	public function get_issuer() {
		return $this->issuer;
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

switch ($_POST['type']) {
    case 'persona':
        $browserid = new BrowserID($issuer_url, filter_var($_POST['a'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
		if($browserid->verify_assertion()) {
			if(!isset($_GET['nosession'])) $_SESSION['personaMail'] = $browserid->get_email();
			echo '{"status":1, "mail":"'.$browserid->get_email().'"}';
		} else {
			// TODO err
			die( '{"status":0, "error":"Identification failure"}' );
		}
    break;
	case 'answer':
		if(empty($_POST['qnumber'])){
			die( '{"status":0, "error":"Which question number?"}' );
		} else {
			$nr = intval(filter_var($_POST['qnumber'], FILTER_SANITIZE_NUMBER_INT));	
		}
		
		if(empty($_POST['answer'])){
			die( '{"status":0, "error":"Empty answer"}' );
		} else {
			$answer = filter_var($_POST['answer'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			if (in_array($answer, $answers[$nr-1])) {
				echo '{"status":1, "message":"Correct answer"}';
			} else {
				die( '{"status":0, "error":"Wrong answer"}' );
			}
		}
		
	break;
    case 'badge':
        //set all variables
		if(empty($_POST['badgename'])){
			die( '{"status":0, "error":"Empty badgename"}' );
		} else {
			$badgeId 			= filter_var($_POST['badgename'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		}
		$badgeRecipientEmail 	= $_SESSION['personaMail'];
		$badgeExperienceURL 	= $issuer_url;
		$badgeName 				= $badges_array[$badgeId]['name'];
		$badgeImage				= $badges_array[$badgeId]['image'];
		$badgeDescription		= $badges_array[$badgeId]['description'];
		$badgeCriteria			= $badges_array[$badgeId]['criteria_url'];
		$badgeExpires			= $badges_array[$badgeId]['expires'];	
		$date = date('Y-m-d');
		$err = '';
		$msg = '';
		
		
		//salt email - randomized everytime
		$IVSIZE = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
		$salt = bin2hex(mcrypt_create_iv($IVSIZE, MCRYPT_DEV_RANDOM));
		//$salt = rand_string(8); //randomized everytime
		$hashed_email = hash('sha256', $badgeRecipientEmail  . $salt);
		
	
		//creates JSON file - will write over an existing badge for same badge and same user.
		$filename = str_rot13(hash('sha256', $badgeId).'-'.$hashed_email);
		$jsonFilePath = $json_dir . $filename .'.json';
	
		$handle = fopen($jsonFilePath, 'w');
		$fileData = array(
			'recipient' => "sha256$".$hashed_email,
			'salt' => $salt,
			'evidence' => $badgeExperienceURL,
			'badge' => array(
				'version' => '0.5.0',
				'name' => $badgeName,
				'image' => $issuer_url.$badge_images_dir.$badgeImage,
				'description' => $badgeDescription,
				'criteria' => $badgeCriteria,
				'issued_on'=> $date,
				'expires' => $badgeExpires,
				'issuer' => array(
					'origin' => $issuer_url,
					'name' => $issuer_name
				)
			)
		);
		
		//Writes JSON file
		if (fwrite($handle, json_encode($fileData)) === FALSE) {
			die( '{"status":0, "error":"Cannot write to file ($jsonFilePath). Please check your \$json_dir in settings.php: "'.$json_dir.'"}' );
	   }
		else { 
			//Sucess message and write badge to badge_records.txt file
			echo '{"status":1, "url":"'.$issuer_url.$json_dir_rel.$filename.'.json"}';
			fclose($handle);
			
			//Write "AWARDED" to badge_records.txt file		
			$badgeHandle = fopen($badge_records_file, 'a'); 
			$badge_data = "BADGE AWARDED: ".$date.", ".$badgeName.", ".$jsonFilePath.", ".$badgeRecipientEmail.", ".$badgeCriteria;
			// $badge_data = "BADGE AWARDED: ".$date.", ".$badgeName.", ".$jsonFilePath.", ".$badgeRecipientName.", ".$badgeRecipientEmail.", ".$badgeCriteria;
		
			if (! empty($badgeExperienceURL)) {
				$badge_data .= ", ".$badgeExperienceURL;
			}
		
			$badge_data .= "\n";
		
			if (fwrite($badgeHandle, $badge_data) === FALSE) {
				die( '{	"status":0, "error":"Cannot write to file ('.$badge_records_file.'). Please check your $root and $badge_records_file in settings.php. Your JSON file was created but the badge awarding was not recorded."}');
		}
		
			fclose($badgeHandle);
					
		}
    break;
}
