<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class adminloginAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "adminLogin",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"email" => array ("type" => "string", "desc" => "") , 
						"password" => array ("type" => "string", "desc" => "") ,
						),
					"optional" => array (
						)
					),
				"out" => array (
					"partner_id" => array ( "type" => "string" , "desc" => "" ),
					"subp_id" => array ( "type" => "string" , "desc" => "" ),
					"uid" => array ( "type" => "string" , "desc" => "" ),
					"ks" => array ( "type" => "string" , "desc" => "" ),
					),
				"errors" => array (
					APIErrors::ADMIN_KUSER_NOT_FOUND,
					APIErrors::LOGIN_RETRIES_EXCEEDED,
					APIErrors::LOGIN_BLOCKED,
					APIErrors::USER_WRONG_PASSWORD,
					APIErrors::PASSWORD_EXPIRED,
					APIErrors::UNKNOWN_PARTNER_ID,
					)
			);
	}

    
	protected function shouldCacheResonse () { return false; }
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		defPartnerservices2baseAction::disableCache();
		kuserPeer::setUseCriteriaFilter(false);
		
		$email = trim ( $this->getPM ( "email" ) );
		$password = trim (  $this->getPM ( "password" ) );
		
		$loginData = UserLoginDataPeer::getByEmail ($email);
		
		// be sure to return the same error if there are no admins in the list and when there are none matched -
		// so no hint about existing admin will leak 
		if ( !$loginData )
		{
			$this->addError ( APIErrors::ADMIN_KUSER_NOT_FOUND );	
			return;
		}

		try {
			$adminKuser = UserLoginDataPeer::userLoginByEmail($email, $password, $partner_id);
		}
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::USER_NOT_FOUND) {
				$this->addError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
				return null;
			}
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				$this->addError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
				return null;
			}
			else if ($code == kUserException::LOGIN_RETRIES_EXCEEDED) {
				$this->addError  ( APIErrors::LOGIN_RETRIES_EXCEEDED );
				return null;
			}
			else if ($code == kUserException::LOGIN_BLOCKED) {
				$this->addError  ( APIErrors::LOGIN_BLOCKED );
				return null;
			}
			else if ($code == kUserException::PASSWORD_EXPIRED) {
				$this->addError  ( APIErrors::PASSWORD_EXPIRED );
				return null;
			}
			else if ($code == kUserException::WRONG_PASSWORD) {
				$this->addError  (APIErrors::ADMIN_KUSER_NOT_FOUND);
			}
			else if ($code == kUserException::USER_IS_BLOCKED) {
				$this->addError  (APIErrors::USER_IS_BLOCKED);
			}
			$this->addError  ( APIErrors::INTERNAL_SERVERL_ERROR );
			return null;
		}
		if (!$adminKuser || !$adminKuser->getIsAdmin()) {
			$this->addError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
			return null;
		}
		
		
		if ($partner_id && $partner_id != $adminKuser->getPartnerId()) {
			$this->addError  ( APIErrors::UNKNOWN_PARTNER_ID );
			return;
		}
		
		$partner = PartnerPeer::retrieveByPK( $adminKuser->getPartnerId() );
		
		if (!$partner)
		{
			$this->addError  ( APIErrors::UNKNOWN_PARTNER_ID );
			return;		
		}
		
		$partner_id = $partner->getId();
		$subp_id = $partner->getSubpId() ;
		$admin_puser_id = $adminKuser->getPuserId();
		
		// get the puser_kuser for this admin if exists, if not - creae it and return it - create a kuser too
		$puser_kuser = PuserKuserPeer::createPuserKuser ( $partner_id , $subp_id, $admin_puser_id , $adminKuser->getScreenName() , $adminKuser->getScreenName(), true);
		$uid = $puser_kuser->getPuserId();
		$ks = null;
		// create a ks for this admin_kuser as if entered the admin_secret using the API
		// ALLOW A KS FOR 30 DAYS
		kSessionUtils::createKSessionNoValidations ( $partner_id ,  $uid , $ks , 30 * 86400 , 2 , "" , "*" );
		
		
		$this->addMsg ( "partner_id" , $partner_id ) ;
		$this->addMsg ( "subp_id" , $subp_id );		
		$this->addMsg ( "uid" , $uid );
		$this->addMsg ( "ks" , $ks );
		$this->addMsg ( "screenName" , $adminKuser->getFullName() );
		$this->addMsg ( "fullName" , $adminKuser->getFullName() );
		$this->addMsg ( "email" , $adminKuser->getEmail() );
	}
}
