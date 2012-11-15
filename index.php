<?php session_start();

//Badge-It AJAX v0.5.0 - Simple scripted system to award and issue badges into Mozilla Open Badges Infrastructure
//Copyright (c) 2012 Sebastian Lasse New Media - sebastianlasse.de
//inspired by Badge-It Gadget Lite
//Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

include 'settings.php';

// RANDOM session-token
function make_seed(){ list($usec, $sec) = explode(' ', microtime()); return (float) $sec + ((float) $usec * 100000); }
mt_srand(make_seed());
$phprand = mt_rand();

define(IVSIZE, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
define(RANDOM_ID, bin2hex(mcrypt_create_iv(IVSIZE, MCRYPT_DEV_RANDOM)));	
define(RANDOM, openssl_digest(RANDOM_ID.$phprand , 'sha512'));
$cookiename = str_replace(".","_",$issuer_host).'_openbadges';
if($issuer_protocol=='https'){
	setcookie ($cookiename, RANDOM_ID, time()+3600, "/", ".".$issuer_host,1,1);
} else {
	setcookie ($cookiename, RANDOM_ID, time()+3600, "/", ".".$issuer_host);
}
if(!isset($_GET['nosession'])) $_SESSION[RANDOM_ID] = RANDOM;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Mozilla Persona, BrowserId and OpenBadges AJAX example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="a testcase for Mozilla Persona (BrowserID) and Open Badges">
    <meta name="author" content="Badger">    
	<link rel="stylesheet" href="./css/style.css">    
  </head>

  <body itemscope itemtype="http://schema.org/Product">
		<div class="light-bg-container">
            <h1 itemprop="name">A simple AJAX Demo to demonstrate Mozilla Persona &amp; Open Badges</h1>
            <div id="loader"><h1 class="orange"><br />loading ...</h1></div>
            <div id="content">
                <h2><br />[ If you do not have an Open Badges account yet: Get started at <a target="_blank" href="http://openbadges.org/">Open Badges</a> ! ]</h2>
                <a class="badge start">I want to earn a badge</a>
            </div>
            <h2>
                <b>1</b> LogIn with <span id="persona"><a target="_blank" href="https://login.persona.org/">Persona</a></span> &nbsp; 
                <b>2</b> Answer question &nbsp;
                <b>3</b> <span id="badge">Get your badge</span>
            </h2>
		</div>
        <div class="hidden">
        	<img itemprop="image" src="./digital-badges/images/standard.png" />
            <p itemprop="description">a testcase for Mozilla Persona (BrowserID) and Open Badges</p>
        </div>
        <script type="text/javascript">var _csrf = { _a:'<?php echo RANDOM; ?>', _b:'<?php echo openssl_digest($_SERVER['REMOTE_ADDR'], 'sha512'); ?>' };</script>
        <script type="text/javascript" src="<?php print $persona_api; ?>"></script>
        <script type="text/javascript" src="<?php print $open_badges_api; ?>"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.8/dojo/dojo.js" data-dojo-config="parseOnLoad: true, async: true, isDebug: false"></script>
    	<script type="text/javascript" src="./js/application.js"></script>
	</body>
</html>