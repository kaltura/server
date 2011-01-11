<?php 

// Load kaltura client
require_once('/opt/kaltura/app/clients/php5/KalturaClient.php');

// select which form to show
switch($_GET['action']) {
	case "password": 
		changePasswordForm();
		break;
	case "email":
		$email = isset($_GET['email']) ? $_GET['email'] : "john@smith.com"; 
		changeEmailForm($email);
		break;
	case "name": 
		$fname = isset($_GET['fname']) ? $_GET['fname'] : "John";
		$lname = isset($_GET['lname']) ? $_GET['lname'] : "Smith";
		changeNameForm($fname, $lname);
		break;
}

function changePasswordForm() {
	showHead('Change Password');
	
	echo <<<HTML
	<br />
	<form method="post">
		<div class="left">Current Password:</div>
		<div class="right"><input type="password" name="cur_password" /></div>
		<br class="clear" />
		<div class="left">New Password:</div>
		<div class="right"><input type="password" name="new_password" /></div>
		<br class="clear" />
		<div class="left">Retry New Password:</div>
		<div class="right"><input type="password" name="retry_new_password" /></div>
		<br class="clear" />
		<div class="center"><input type="submit" value="Save Changes" /></div><br />
	</form>
HTML;
	
	showFoot();
}

function changeEmailForm($email) {
	showHead('Change Email Address');

	echo <<<HTML
	<br />
	<form method="post">
		<div class="left">Current email address:</div>
		<div class="right">{$email}</div>
		<br class="clear" />
		<div class="left">Edit email address:</div>
		<div class="right"><input type="text" name="email" value="{$email}" /></div>
		<br class="clear" />
		<div class="left">Password:</div>
		<div class="right"><input type="password" name="password" /></div>
		<br class="clear" />
		<div>Password is required for editing your email address.</div><br />
		<div class="center"><input type="submit" value="Save Changes" /></div><br />
	</form>
HTML;
	
	showFoot();
}

function changeNameForm($fname, $lname) {
	showHead('Change Username');

	echo <<<HTML
	<br />
	<form method="post">
		<div class="left">Current name:</div>
		<div class="right">{$fname} {$lname}</div>
		<br class="clear" />
		<div class="left">Edit First Name:</div>
		<div class="right"><input type="text" name="first_name" value="{$fname}" /></div>
		<br class="clear" />
		<div class="left">Edit Last Name:</div>
		<div class="right"><input type="text" name="last_name" value="{$lname}" /></div>
		<br class="clear" />		
		<div class="left">Password:</div>
		<div class="right"><input type="password" name="password" /></div>
		<br class="clear" />
		<div>Password is required for editing your name.</div><br />
		<div class="center"><input type="submit" value="Save Changes" /></div><br />
	</form>
HTML;
		
	showFoot();
}

function showHead($title) {

 echo <<<HTML
<html>
<head>
	<title>{$title}</title>
	<meta charset="utf-8" />
	<style>
	html, body { margin: 0; padding: 0; width: 100%; height: 100%; }
	body { background:#F8F8F8; font: 13px arial,sans-serif; }
	.left { float: left; width: 48%; text-align: left; margin: 5px 0; padding: 0 0 0 4px; }
	.right { float: right; width: 48%; text-align: left; margin: 5px 0; padding: 0 0 0 4px; }
	.center { text-align: center; }
	.clear { clear: both; }
	</style>
</head>
<body>
	<div id="wrapper">
HTML;

}

function showFoot() {

 echo <<<HTML
 	</div>
 </body></html>
HTML;

}

?>