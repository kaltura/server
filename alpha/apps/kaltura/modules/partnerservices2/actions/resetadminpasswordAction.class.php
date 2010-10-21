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

    // TODO - maybe see that the password doesn't change too often 
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		defPartnerservices2baseAction::disableCache();
		
		$email = trim ( $this->getPM ( "email" ) );
		try {	
			$akp = new adminKuserPeer(); // TODO - why not static ?
			list( $new_password , $new_email ) = $akp->resetUserPassword ( $email  );
		}		
		catch (kAdminKuserException $e) {
			$code = $e->getCode();
			if ($code == kAdminKuserException::ADMIN_KUSER_NOT_FOUND) {
				$this->addException( APIErrors::ADMIN_KUSER_NOT_FOUND );
				return null;
			}
			if ($code == kAdminKuserException::ADMIN_KUSER_WRONG_OLD_PASSWORD) {
				$this->addException( APIErrors::ADMIN_KUSER_WRONG_OLD_PASSWORD );
				return null;
			}
			if ($code == kAdminKuserException::PASSWORD_STRUCTURE_INVALID) {
				$this->addException( APIErrors::PASSWORD_STRUCTURE_INVALID );
				return null;
			}
			if ($code == kAdminKuserException::PASSWORD_ALREADY_USED) {
				$this->addException( APIErrors::PASSWORD_ALREADY_USED );
				return null;
			}
		}
		
		if ( ! $new_password )
		{
			$this->addException( APIErrors::ADMIN_KUSER_NOT_FOUND );
		}
		$this->addMsg ( "msg" , "email sent") ;
	}
}
?>