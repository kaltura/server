<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");
require_once ( "webservices/kSessionUtils.class.php");

class getpartnerAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "getPartner",
				"desc" => "Get the data of a partner that belongs to an admin given his email and password and partner_id." ,
				"in" => array (
					"mandatory" => array (
						"partner_adminEmail" => array ("type" => "string", "desc" => ""),
						"cms_password" 		=> array ("type" => "string", "desc" => ""),
						"partner_id" 		=> array ("type" => "integer", "desc" => ""),
						),
					"optional" => array (
						"detailed" => array ("type" => "string", "desc" => ""),
						)
					),
				"out" => array (
					"partner" => array ("type" => "Partner", "desc" => ""),
					"subp_id" => array ("type" => "string", "desc" => ""),					
					"html_message" => array ("type" => "string", "desc" => ""),
					),
				"errors" => array (
				 	APIErrors::ADMIN_KUSER_NOT_FOUND,
				 	
				)
			);
	}

	protected function ticketType ()	{		return self::REQUIED_TICKET_NONE;	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	protected function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}

	protected function allowEmptyPuser()	{		return true;	}
		
	// Becuase of the sensitive data that is returned from this service - there should be a way to force high security
		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// make sure the secret fits the one in the partner's table
		$partner_adminEmail = trim ( $this->getPM ( "partner_adminEmail" ) );
		$cms_password = trim (  $this->getPM ( "cms_password" ) );
		$detailed = trim ( $this->getP ( "detailed" , "true" , true )); 		
		if ( $detailed === "0" || $detailed === "false" ) $detailed = false;

		if ( empty ( $partner_id ) ) 
		{
			$this->addError( APIErrors::MANDATORY_PARAMETER_MISSING , "partner_id" );
			return;	
		}
		
		$c = new Criteria();
		$c->add ( adminKuserPeer::EMAIL , $partner_adminEmail );
		$c->add ( adminKuserPeer::PARTNER_ID , $partner_id );
		$c->setLimit ( 20 ); // just to limit the number of partners returned
		$admin = adminKuserPeer::doSelectOne( $c );
		
		// be sure to return the same error if there are no admins in the list and when there are none matched -
		// so no hint about existing admin will leak 
		if ( count ( $admin ) < 1 )
		{
			$this->addError ( APIErrors::ADMIN_KUSER_NOT_FOUND );	
			return;
		}

		if ( ! $admin->isPasswordValid ( $cms_password ))
		{
			$this->addError ( APIErrors::ADMIN_KUSER_NOT_FOUND );	
			return;			
		}
		
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		
		$partner_registration = new myPartnerRegistration ();
		$partner_registration->sendRegistrationInformationForPartner( $partner , $partner->getSubp() , $cms_password , true );
		
		$subpid = $partner_id * 100;
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		$wrapper = objectWrapperBase::getWrapperClass( $partner , $level );
		$this->addMsg ( "partner" , $wrapper ) ;
		$this->addMsg ( "html_message" , "" ) ;
		$this->addMsg ( "subp_id" , $partner->getSubp() );
	}
}
?>