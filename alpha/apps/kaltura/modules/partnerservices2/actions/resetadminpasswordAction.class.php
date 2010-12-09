<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class resetadminpasswordAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "resetAdminPassword",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"email" => array ("type" => "string", "desc" => "") , 
						
						),
					"optional" => array (
						)
					),
				"out" => array (
					"new_password" => array ( "type" => "string" , "desc" => "" ),
					),
				"errors" => array (
					)
			);
	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		defPartnerservices2baseAction::disableCache();
		
		$email = trim ( $this->getPM ( "email" ) );
		try {	
			list( $new_password , $new_email ) = UserLoginDataPeer::resetUserPassword ( $email  );
		}		
		catch (kUserException $e) {
			$code = $e->getCode();
			if ($code == kUserException::LOGIN_DATA_NOT_FOUND) {
				$this->addException( APIErrors::ADMIN_KUSER_NOT_FOUND );
				return null;
			}
			if ($code == kUserException::PASSWORD_STRUCTURE_INVALID) {
				$this->addException( APIErrors::PASSWORD_STRUCTURE_INVALID );
				return null;
			}
			if ($code == kUserException::PASSWORD_ALREADY_USED) {
				$this->addException( APIErrors::PASSWORD_ALREADY_USED );
				return null;
			}
			if ($code == kUserException::INVALID_EMAIL) {
				$this->addException( APIErrors::INVALID_FIELD_VALUE, 'email' );
				return null;
			}
			if ($code == kUserException::LOGIN_ID_ALREADY_USED) {
				$this->addException( APIErrors::LOGIN_ID_ALREADY_USED);
				return null;
			}			
			throw $e;
		}
		
		if ( ! $new_password )
		{
			$this->addException( APIErrors::ADMIN_KUSER_NOT_FOUND );
		}
		$this->addMsg ( "msg" , "email sent") ;
	}
}
?>