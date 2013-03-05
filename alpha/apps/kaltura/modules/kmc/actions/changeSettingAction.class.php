<?php
/**
 * @package    Core
 * @subpackage KMC
 */
require_once ( "kalturaAction.class.php" );

/**
 * @package    Core
 * @subpackage KMC
 */
class changeSettingAction extends kalturaAction
{
	public function execute() 
	{
		// Disable layout
		$this->setLayout(false);
		$this->success = false;

		$this->type = $this->getRequestParameter('type');
		if(!$this->type)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'type');

		$validTypes = array('name', 'email' ,'password');
		if(! in_array($this->type, $validTypes))
			KExternalErrors::dieError('INVALID_TYPE', 'Invalid setting type');

		$ks = $this->getP ( "kmcks" );
		if(!$ks)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'ks');

		// Get partner & user info from KS
		$ksObj = kSessionUtils::crackKs($ks);
		$partnerId = $ksObj->partner_id;
		$userId = $ksObj->user;

		// Load the current user
		$dbUser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
	
		if (!$dbUser)
			KExternalErrors::dieError('INVALID_USER_ID', $userId);;

		$this->email = $dbUser->getEmail();
		$this->fname = $dbUser->getFirstName();
		$this->lname = $dbUser->getLastName();	

		// Set page title
		switch($this->type) {
			case 'password': 
				$this->pageTitle = 'Change Password';
				break;

			case 'email':
				$this->pageTitle = 'Change Email Address';
				break;

			case 'name': 
				$this->pageTitle = 'Change Username'; 
				break;
		}

		// select which action to do
		if( isset($_POST['do']) ) {
			
			switch($_POST['do']) {
				
				case "password": 
					$this->changePassword();
					break;
					
				case "email":
					$this->changeEmail();
					break;
					
				case "name": 
					$this->changeName();
					break;
			}	
		}

		sfView::SUCCESS;
	}

	private function changePassword() 
	{
		// Checks if we have empty fields
		$required = array('cur_password', 'new_password', 'retry_new_password');
		foreach($required as $req) {
			if( empty(trim($_POST[$req])) ) {
				$this->setError('You must fill all the fields.');
				return;
			}
		}
		
		if( $_POST['new_password'] !== $_POST['retry_new_password'] ) {
			$this->setError('The passwords does not match!');
			return;
		}
		
		/*
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
		*/
	}

	private function changeEmail()
	{

	}

	private function changeName()
	{

	}

	private function setSuccess() 
	{
		$this->success = true;
		$this->parent_url = $this->clean($_GET['parent']);
		if($this->type == 'password') {
			$this->msg = "close";
		} else {
			$this->msg = "reload";			
		}
	}

	private function setError($error) 
	{
		$error = str_replace("&lt;", "<", $error);
		$error = str_replace("&gt;", ">", $error);
		$this->error = $error;
	}

	private function clean($str) 
	{ 
		$str = str_replace("javascript:", "", $str);
		$str = str_replace("eval", "", $str);
		$str = str_replace("document", "", $str);
		$str = htmlspecialchars($str);
		$str = addslashes($str);
		
		return $str;
	}
}