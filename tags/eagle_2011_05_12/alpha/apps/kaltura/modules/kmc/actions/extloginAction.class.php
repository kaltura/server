<?php
class extloginAction extends kalturaAction
{
	
	private function dieOnError($error_code)
	{
		if ( is_array ( $error_code ) )
		{
			$args = $error_code;
			$error_code = $error_code[0];
		}
		else
		{
			$args = func_get_args();
		}
		array_shift($args);
		
		$error = explode(",", $error_code, 2);
		
		$error_code = $error[0];
		$error_message = $error[1];

		$formated_desc = @call_user_func_array('sprintf', array_merge((array)$error_message, $args)); 
		
		header("X-Kaltura:error-$error_code");
		header('X-Kaltura-App: exiting on error '.$error_code.' - '.$formated_desc);
		
		die();
	}
	
	public function execute()
	{
		$ks = $this->getP ( "ks" );
		$requestedPartnerId = $this->getP ( "partner_id" );
		
		$ksObj = kSessionUtils::crackKs($ks);
		$ksPartnerId = $ksObj->partner_id;
		
		if (!$requestedPartnerId) {
			$requestedPartnerId = $ksPartnerId; 
		}
		
		try {
			$adminKuser = UserLoginDataPeer::userLoginByKs($ks, $requestedPartnerId, true);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::USER_NOT_FOUND) {
				$this->dieOnError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
			}
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				$this->dieOnError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
			}
			else if ($code == kUserException::LOGIN_RETRIES_EXCEEDED) {
				$this->dieOnError  ( APIErrors::LOGIN_RETRIES_EXCEEDED );
			}
			else if ($code == kUserException::LOGIN_BLOCKED) {
				$this->dieOnError  ( APIErrors::LOGIN_BLOCKED );
			}
			else if ($code == kUserException::PASSWORD_EXPIRED) {
				$this->dieOnError  ( APIErrors::PASSWORD_EXPIRED );
			}
			else if ($code == kUserException::WRONG_PASSWORD) {
				$this->dieOnError  (APIErrors::ADMIN_KUSER_NOT_FOUND);
			}
			else if ($code == kUserException::USER_IS_BLOCKED) {
				$this->dieOnError  (APIErrors::USER_IS_BLOCKED);
			}
			$this->dieOnError  ( APIErrors::INTERNAL_SERVERL_ERROR );
		}
		if (!$adminKuser || !$adminKuser->getIsAdmin()) {
			$this->dieOnError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
		}
		
		
		if ($requestedPartnerId != $adminKuser->getPartnerId()) {
			$this->dieOnError  ( APIErrors::UNKNOWN_PARTNER_ID );
		}
		
		$partner = PartnerPeer::retrieveByPK( $adminKuser->getPartnerId() );
		
		if (!$partner)
		{
			$this->dieOnError  ( APIErrors::UNKNOWN_PARTNER_ID );	
		}
		
		$partner_id = $partner->getId();
		$subp_id = $partner->getSubpId() ;
		$admin_puser_id = $adminKuser->getPuserId();
		$screen_name = $adminKuser->getScreenName();
		
		if (!$screen_name)
		{
			// for backward compatibility
			$screen_name = $this->getP ( "screen_name" );
		}
		
		$noUserInKs = is_null($ksObj->user) || $ksObj->user === '';
		if ( ($ksPartnerId != $partner_id) || ($partner->getKmcVersion() >= 4 && $noUserInKs) )
		{
			$ks = null;	
			$sessionType = $adminKuser->getIsAdmin() ? SessionType::ADMIN : SessionType::USER;
			kSessionUtils::createKSessionNoValidations ( $partner_id ,  $admin_puser_id , $ks , 30 * 86400 , $sessionType , "" , "*" );
		}
					
		$exp = 0;
		$path = "/";
		
		$this->getResponse()->setCookie("pid", $partner_id, $exp, $path);
		$this->getResponse()->setCookie("subpid", $subp_id, $exp, $path);
		$this->getResponse()->setCookie("uid", $admin_puser_id, $exp, $path);
		$this->getResponse()->setCookie("kmcks", $ks, $exp, $path);
		$this->getResponse()->setCookie("screen_name", $screen_name, $exp, $path);
		$this->redirect('kmc/kmc2');
	}
	
}
