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
					APIErrors::ADMIN_KUSER_NOT_FOUND
					)
			);
	}

    
	protected function shouldCacheResonse () { return false; }
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$email = trim ( $this->getPM ( "email" ) );
		$password = trim (  $this->getPM ( "password" ) );
		
		$admin = adminKuserPeer::getAdminKuserByEmail ( $email , true );
		
		// be sure to return the same error if there are no admins in the list and when there are none matched -
		// so no hint about existing admin will leak 
		if ( count ( $admin ) < 1 )
		{
			$this->addError ( APIErrors::ADMIN_KUSER_NOT_FOUND );	
			return;
		}

		try {
			$adminKuser = adminKuserPeer::adminLogin($email, $password);
		}
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				$this->addError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
				return null;
			}
			else if ($code == kAdminKuserException::LOGIN_RETRIES_EXCEEDED) {
				$this->addError  ( APIErrors::LOGIN_RETRIES_EXCEEDED );
				return null;
			}
			else if ($code == kAdminKuserException::LOGIN_BLOCKED) {
				$this->addError  ( APIErrors::LOGIN_BLOCKED );
				return null;
			}
			else if ($code == kAdminKuserException::PASSWORD_EXPIRED) {
				$this->addError  ( APIErrors::PASSWORD_EXPIRED );
				return null;
			}
			$this->addError  ( APIErrors::INTERNAL_SERVERL_ERROR );
			return null;
		}
		if (!$adminKuser) {
			$this->addError  ( APIErrors::ADMIN_KUSER_NOT_FOUND );
			return null;
		}
		
		$partner = PartnerPeer::retrieveByPK( $adminKuser->getPartnerId() );
		
		if ( ! $partner )
		{
			$this->addError  ( APIErrors::UNKNOWN_PARTNER_ID );
			return;		
		}
		
		$partner_id = $partner->getId();
		$subp_id = $partner->getSubpId() ;
		$admin_puser_id = "__ADMIN__" . $admin->getId(); // the prefix __ADMIN__ and the id in the admin_kuser table
		
		// get the puser_kuser for this admin if exists, if not - creae it and return it - create a kuser too
		$puser_kuser = PuserKuserPeer::createPuserKuser ( $partner_id , $subp_id, $admin_puser_id , $admin->getScreenName() , $admin->getScreenName(), true );
		$uid = $puser_kuser->getPuserId();
		$ks = null;
		// create a ks for this admin_kuser as if entered the admin_secret using the API
		// ALLOW A KS FOR 30 DAYS
		kSessionUtils::createKSessionNoValidations ( $partner_id ,  $uid , $ks , 30 * 86400 , 2 , "" , "*" );
		
		
		$this->addMsg ( "partner_id" , $partner_id ) ;
		$this->addMsg ( "subp_id" , $subp_id );		
		$this->addMsg ( "uid" , $uid );
		$this->addMsg ( "ks" , $ks );
		$this->addMsg ( "screenName" , $admin->getFullName() );
		$this->addMsg ( "fullName" , $admin->getFullName() );
		$this->addMsg ( "email" , $admin->getEmail() );
	}
}
?>