<?php

//Badge-It AJAX v0.5.0 - Simple scripted system to award and issue badges into Mozilla Open Badges Infrastructure
//Copyright (c) 2012 Sebastian Lasse New Media - sebastianlasse.de
//inspired by Badge-It Gadget Lite
//Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

/*** 

This is the settings file ...
Read it carefully and put in your values
Read more about Open Badges Assertions here: https://github.com/mozilla/openbadges/wiki/Assertions 

you SHOULD run this example via https and take care, nobody can issue badges in your name
***/

/* The secret ;) possible answers to the question which is used only for demo purposes - 
// you can manage multiple questions by pushing more arrays to the answer array */
$answers = array();
array_push( $answers, 	array('2','02','two','zwei','dos','deux','U+0032') );

/* REQUIRED - Persona API url - This is BrowserID API */
$persona_api = "https://browserid.org/include.js";

/* REQUIRED - OB Issuer API url - This is Open Badge's hosted issuer API. */
$open_badges_api = "http://beta.openbadges.org/issuer.js";
/* REQUIRED - version - Use "0.5.0" for the Open Badges beta. */
$version = "0.5.0";

/* REQUIRED - Issuer name - name of organization or person that is issuing the badges. */
// This appears on the badge
$issuer_name = "John Doe"; 

/* OPTIONAL - Issuer org - Organization for which the badge is being issued. 
// Another example is if a scout badge is being issued, "name" could be "Boy Scouts" and "org" could be "Troop #218". */
$issuer_org = "John Doe New Media";

/* OPTIONAL - Issuer contact - A human-monitored email address associated with the issuer. */
$issuer_contact = "";

/* REQUIRED - Issuer host - This is the domain name of the site that will be issuing the badges. 
// It should be the domain where you're installing this. */
$issuer_protocol = "http";
$issuer_host = "yourdomain.com";

/* REQUIRED - JSON file directory 		-CHMOD 777. 
// We generate JSON file for each issued badge (per person). 
// The JSON files need to be in a publicly accessible but not obvious directory. This should start at the document root. 
// NOTE: your server may require the path to have a forward slash and note the slash at the end of the path */
$json_dir_rel = "/OpenBadgesAjax/digital-badges/issued/json/";

/* REQUIRED - Badge images directory 	-CHMOD 775
// Set the path to the directory where your badge images are stored. 
// They should be stored on the issuing domain. */
$badge_images_dir = "/OpenBadgesAjax/digital-badges/images/";

/* REQUIRED - Badge records file 		-CHMOD 777. 
// We will keep records in a text file of which badges were issued. 
// This could easily be extended to use a db later.*/
// NOTE: your server may require the path to have a forward slash */
$badge_records_file = $_SERVER['DOCUMENT_ROOT']."/OpenBadgesAjax/digital-badges/issued/badge_records.txt";

/* BADGES!! - this is the array to store badges data. 
info on how to learn about arrays in php: http://devzone.zend.com/8/php-101-part-4-the-food-factor/ 

Here are the values (all REQUIRED unless noted otherwise):

- name
The name of your badge. Example "Javascript Geek Badge" (max 128 characters)
- description
Short text describing the badge. Example "Earner is now an Open Badges Issuer." (max 128 characters)
- image
The filename of the image. Example "badge.png". This image should be in your $badge_images_dir. (must be .png) 
- criteria_url
Relative URL describing the badge and criteria for earning the badge. It should be on the issuing server.
- expires
OPTIONAL. Date when the badge expires. If omitted, the badge never expires. Format: YYYY-MM-DD

Notice there is a number and an array of values for each badge. 
The example below has two badges. You specify the badge in the Javascript (e.g. postArgs.badgename = 'standard') !
*/

$badges_array = array(
	"standard" => array(
		"name" => "AJAX example Badge", 
		"description" => "Earner is ready to award badges with AJAX.",
		"image" => "standard.png", 
		"criteria_url" => $_SERVER['DOCUMENT_ROOT']."/OpenBadgesAjax/digital-badges/badge-criteria.html",
		"expires" => "2015-31-12"
	), 
	"other" => array(
		"name" => "Another Badge", 
		"image" => "example.png", 
		"description" => "This is an example of another badge", 
		"criteria_url" => "/digital-badges/example-badge-not-real.html"
	) 
);





// DO NOT edit the following lines
$issuer_url = $issuer_protocol."://".$issuer_host."/";
$json_dir = $_SERVER['DOCUMENT_ROOT'].$json_dir_rel;