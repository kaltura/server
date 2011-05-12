<?php 

// Check if the user logged in
if( !isset($_COOKIE['kmcks']) || empty($_COOKIE['kmcks']) ) {
	die('Not logged in!');
}

$sForm = new secForm();

class secForm {

	var $pageTitle;
	var $email;
	var $fname;
	var $lname;
	var $partnerId;
	var $userId;
	var $Ks;
	var $error;
	var $curAction;
	
	function secForm() {
		//echo '<pre>'; print_r( explode(";", base64_decode($_COOKIE['kmcks'])) ); exit();
		// Get data from cookies
		$this->email = $_COOKIE['email'];
		$this->partnerId = $_COOKIE['pid'];
		$this->userId = $_COOKIE['uid'];
		$this->Ks = $_COOKIE['kmcks'];

		// Get data from url parameters
		$this->email = isset($_GET['email']) ? $this->clean($_GET['email']) : "";
		$this->fname = isset($_GET['fname']) ? $this->clean($_GET['fname']) : "";
		$this->lname = isset($_GET['lname']) ? $this->clean($_GET['lname']) : "";
						
		// select which action to do
		if( isset($_POST['do']) ) {
			
			switch($_POST['do']) {
				
				case "password": 
					$this->doChangePassword();
					break;
					
				case "email":
					$this->doChangeEmail();
					break;
					
				case "name": 
					$this->doChangeName();
					break;
			}	
		}
		
		// select which form to show
		switch($_GET['action']) {
			case "password": 
				$this->showChangePassword();
				break;
			case "email":
				$this->showChangeEmail();
				break;
			case "name": 
				$this->showChangeName();
				break;
		}
			
	}
	
	// Return a Client with KS
	function getClient() {

		// Get kaltura configuration file
		require_once( realpath( dirname(__FILE__) ) . '/../config/kConf.php' );
		$kConf = new kConf();
		
		// Load kaltura client
		require_once( realpath( dirname(__FILE__) ) . '/../../clients/php5/KalturaClient.php' );
				
		try {		
			$conf = new KalturaConfiguration( $this->partnerId );
			$conf->serviceUrl = 'http://' . $kConf->get('www_host');
			$client = new KalturaClient( $conf );
			$client->setKS( $this->Ks );
			
		} catch( Exception $e ){
			$this->error = 'Error setting KS. <a href="'.$_SERVER['SCRIPT_NAME'].'">Try again</a>';
			die($this->error);
			return false;
		}	
		return $client;		
	}	
	
	function clean($str) { 
		$str = str_replace("javascript:", "", $str);
		$str = str_replace("eval", "", $str);
		$str = str_replace("document", "", $str);
		$str = htmlspecialchars($str);
		$str = addslashes($str);
		
		return $str;
	}
	
	// Handle errors
	function errorDiv() {
		if( isset($this->error) && !empty($this->error) ) { 
		    //echo '<pre>'; print_r($_COOKIE); exit();
		    //return '<div class="error">' . $this->error . '</div><br />';
		    $error = str_replace("&lt;", "<", $this->error);
		    $error = str_replace("&gt;", ">", $error);
		    return '<script>alert(' . json_encode($error) . ');</script>';
		} else {
		    return '';
		}		
	}

	// Show Change Password Form
	function showChangePassword() {
		$this->pageTitle = 'Change Password';
		$this->showHead();
		
		echo <<<HTML
		<form method="post"><br />
			<input type="hidden" name="do" value="password" />
			<div class="left">Current Password:</div>
			<div class="right"><input id="focused" type="password" name="cur_password" /></div>
			<br class="clear" />
			<div class="left">New Password:</div>
			<div class="right"><input type="password" name="new_password" /></div>
			<br class="clear" />
			<div class="left">Retry New Password:</div>
			<div class="right"><input type="password" name="retry_new_password" /></div>
			<br class="clear" /><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />
			{$this->errorDiv()}
		</form>
HTML;
		
		$this->showFoot();
	}
		
	// Do Change Password
	function doChangePassword() {
		// Set current action
		$this->curAction = 'password';
		
		// Checks if we have empty fields
		$required = array('cur_password', 'new_password', 'retry_new_password');
		foreach($required as $req) {
			if( empty($_POST[$req]) ) {
				$this->error = 'You must fill all the fields.';
				$this->showChangePassword();
				exit();
				break;
			}
		}
		
		if( $_POST['new_password'] != $_POST['retry_new_password'] ) {
			$this->error = "The passwords does not match!";
			$this->showChangePassword();
			exit();
		}
		
		$client = $this->getClient();
		try {
			//updateLoginData accepts [oldUserID, oldPassword, newUserID, newPassword, newFirstName, newLastName)
			$client->user->updateLoginData($this->email, $_POST['cur_password'], null, $_POST['new_password'], null, null);
			
			// Show success message
			$this->showSuccess();
			exit();
						
		} catch( Exception $e ){
			$this->error = $e->getMessage();
			$this->showChangePassword();
			exit();
		}
	}
	
	// Show Change Email Form
	function showChangeEmail() {
		$this->pageTitle = 'Change Email Address';
		$this->showHead();
	
		echo <<<HTML
		<form method="post">
			<input type="hidden" name="do" value="email" />
			<div class="left">Current email address:</div>
			<div class="right current truncated" title="{$this->email}">{$this->email}</div>
			<br class="clear" />
			<div class="left">Edit email address:</div>
			<div class="right"><input id="focused" type="text" name="email" value="{$this->email}" /></div>
			<br class="clear" />
			<div class="left">Password:</div>
			<div class="right"><input type="password" name="password" /></div>
			<br class="clear" />
			<div class="note">* Password is required for editing your email address.</div><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />
			{$this->errorDiv()}			
		</form>
HTML;
		
		$this->showFoot();
	}
	
	// Do Change Email
	function doChangeEmail() {
		// Set current action
		$this->curAction = 'email';	

			// Checks if we have empty fields
		$required = array('email', 'password');
		foreach($required as $req) {
			if( empty($_POST[$req]) ) {
				$this->error = 'You must fill all the fields.';
				$this->showChangeEmail();
				exit();
				break;
			}
		}
		$client = $this->getClient();
		try {
			$client->user->updateLoginData($this->email, $_POST['password'], $_POST['email'], null, null, null);
			
			// Show success message
			$this->showSuccess();
			exit();
						
		} catch( Exception $e ){
			$this->error = $e->getMessage();
			$this->showChangeEmail();
			exit();
		}		
	}
	
	// Show Change Name Form
	function showChangeName() {
		$this->pageTitle = 'Change Username'; 
		$this->showHead();
	
		echo <<<HTML
		<form method="post">
			<input type="hidden" name="do" value="name" />
			<div class="left">Current name:</div>
			<div class="right current truncated" title="{$this->fname} {$this->lname}">{$this->fname} {$this->lname}</div>
			<br class="clear" />
			<div class="left">Edit First Name:</div>
			<div class="right"><input type="text" name="fname" value="{$this->fname}" /></div>
			<br class="clear" />
			<div class="left">Edit Last Name:</div>
			<div class="right"><input type="text" name="lname" value="{$this->lname}" /></div>
			<br class="clear" />
			<div class="left">Password:</div>
			<div class="right"><input type="password" name="password" /></div>
			<br class="clear" />
			<div class="note">* Password is required for editing your name.</div><br />
			<div class="center"><button type="submit" id="submit"><span>Save</span></button></div><br />
			{$this->errorDiv()}			
		</form>
HTML;
			
		$this->showFoot();
	}
	
	// Do Change Name
	function doChangeName() {
		// Set current action
		$this->curAction = 'name';		

		// Checks if we have empty fields
		$required = array('fname', 'lname', 'password');
		foreach($required as $req) {
			if( empty($_POST[$req]) ) {
				$this->error = 'You must fill all the fields.';
				$this->showChangeName();
				exit();
				break;
			}
		}
		
		$client = $this->getClient();
		try {
			
			// Changing name
			$client->user->updateLoginData($this->email, $_POST['password'], null, null, $_POST['fname'], $_POST['lname']);
			setcookie("screen_name", $_POST['fname'] . ' ' . $_POST['lname'] );
			
			// Show success message
			$this->showSuccess();
			exit();
			
		} catch( Exception $e ){
			//echo '<pre>'; print_r($e); exit();
			// Show error
			$this->error = $e->getMessage();
			$this->showChangeName();
			exit();
		}		
	}
	
	// Show Success Message
	function showSuccess() {
		$parent_url = $this->clean($_GET['parent']);
		$this->pageTitle = 'Changes were saved!'; 
		$this->showHead();
		
		// When changing password we only closing the modal
		// Otherwise clode the modal & reload the page
		if($this->curAction == 'password') {
			$msg = "close";
		} else {
			$msg = "reload";			
		}
	
		// We're using postMessage to pass data to the parent document
		echo <<<HTML
<script type="text/javascript" src="/lib/js/postmessage.js"></script>
<script type="text/javascript">
var parent_url = decodeURIComponent("{$parent_url}");

function send() {
	XD.postMessage("{$msg}", parent_url, parent);
}

window.onload = send; 
</script>
HTML;
		$this->showFoot();
	}	
	
	// Show Layout Header
	function showHead() {
	
	 echo <<<HTML
	<html>
	<head>
		<title>{$this->pageTitle}</title>
		<meta charset="utf-8" />
		<style>
		html, body { margin: 0; padding: 0; width: 100%; height: 100%; }
		body { background:#F8F8F8; font: 13px arial,sans-serif; }
		#wrapper { padding: 0 10px; }
		.left { float: left; text-align: left; width: 140px; margin: 5px 0; padding: 4px 0 0 0; }
		.right { float: left; text-align: left; margin: 5px 20px 5px 0; padding: 0; }
		.current { padding-top: 4px; padding-left: 2px; width: 155px; color: #666666; }
		.note { font-size: 11px; color: #999999; }
		.center { text-align: center; }
		.clear { clear: both; }
		.error { color: #ff0000; font-weight: bold; font-size: 12px; margin-bottom: -10px; }
		input { font-size: 13px; width: 170px; }
		.truncated { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
		button { margin: 0 auto 5px; padding: 0 28px 0 0; height: 24px; border: 0; font: normal 11px arial,sans-serif; color:#2B2B2B; line-height: normal; overflow: visible; background: url(lib/images/kmc/kmc_sprite.png) no-repeat -72px -152px; cursor: pointer; }
		button span { height:20px; padding: 4px 0 0 28px; margin: 1px 1px 0 0; float:left; white-space:nowrap; background:transparent url(lib/images/kmc/kmc_sprite.png) no-repeat scroll 0 -153px;}
		button:hover span { background-position: 0 -178px;}
		@-moz-document url-prefix() {
	   		button span { margin: 0 2px 0 -3px; }
	   		button { display: block; padding-right: 20px; }
	   	}
		</style>
		<script>
		function focusFirstInput() {
			// check all the input in the form
			for(i=0; i < document.forms[0].length; i++)
			{
			  // check if input is not hidden & not disabled
			  if ( (document.forms[0][i].type != "hidden") && (document.forms[0][i].disabled != true) )
			  {
				document.forms[0][i].focus();
				break;
			  }
			}
		}
		</script>
	</head>
	<body onload="focusFirstInput();">
		<div id="wrapper">
HTML;
	
	}
	
	// Show Layout Footer
	function showFoot() {
	
	 echo <<<HTML
	 	</div>
	 </body></html>
HTML;
	
	}
}

?>