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
		$email = trim ( $this->getPM ( "email" ) );
		
		$akp = new adminKuserPeer(); // TODO - why not static ?
		$new_password = $akp->resetUserPassword ( $email  );
		if ( ! $new_password )
		{
			$this->addException( APIErrors::ADMIN_KUSER_NOT_FOUND );
		}
		$this->addMsg ( "msg" , "email sent") ;
	}
}
?>