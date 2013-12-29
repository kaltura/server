<?php
/**
 * @package    Core
 * @subpackage KMC
 */
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
		
		$errorData = APIErrors::getErrorData( $error_code, $args );
		$error_code = $errorData['code'];
		$formated_desc = $errorData['message'];
		
		header("X-Kaltura:error-$error_code");
		header('X-Kaltura-App: exiting on error '.$error_code.' - '.$formated_desc);
		
		die();
	}
	
	public function execute()
	{
		$ks = $this->getP ( "ks" );
		if(!$ks)
			$this->dieOnError  ( APIErrors::MISSING_KS );
			
		$requestedPartnerId = $this->getP ( "partner_id" );
		
		$expired = $this->getP ( "exp" );

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
		
		if (!$partner->validateApiAccessControl())
		{
			$this->dieOnError  ( APIErrors::SERVICE_ACCESS_CONTROL_RESTRICTED );
		}
		
		$partner_id = $partner->getId();
		$subp_id = $partner->getSubpId() ;
		$admin_puser_id = $adminKuser->getPuserId();
		
		$exp = (isset($expired) && is_numeric($expired)) ? time() + $expired: 0;
		
		$noUserInKs = is_null($ksObj->user) || $ksObj->user === '';
		if ( ($ksPartnerId != $partner_id) || ($partner->getKmcVersion() >= 4 && $noUserInKs) )
		{
			$ks = null;
			$sessionType = $adminKuser->getIsAdmin() ? SessionType::ADMIN : SessionType::USER;
			kSessionUtils::createKSessionNoValidations ( $partner_id ,  $admin_puser_id , $ks , 30 * 86400 , $sessionType , "" , "*," . kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT );
		}
		
		
		$path = "/";
		$domain = null;
		$force_ssl = PermissionPeer::isValidForPartner(PermissionName::FEATURE_KMC_ENFORCE_HTTPS, $partner_id);
		$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $force_ssl) ? true : false;
		$http_only = true;
		
		$this->getResponse()->setCookie("pid", $partner_id, $exp, $path, $domain, $secure, $http_only);
		$this->getResponse()->setCookie("subpid", $subp_id, $exp, $path, $domain, $secure, $http_only);
		$this->getResponse()->setCookie("kmcks", $ks, $exp, $path, $domain, $secure, $http_only);

		$redirect_url =  ($force_ssl) ? 'https' : 'http';
		$redirect_url .= '://' . $_SERVER["HTTP_HOST"] . '/index.php/kmc/kmc2';
		$this->redirect($redirect_url);
	}
	
}
