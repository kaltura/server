<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");
require_once ( "myPartnerRegistration.class.php");

class registerpartnerAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "registerPartner",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"partner" => array ("type" => "Partner", "desc" => ""),
						),
					"optional" => array (
						"cms_password" => array ("type" => "string", "desc" => "")
						)
					),
				"out" => array (
					"partner" => array ("type" => "Partner", "desc" => ""),
					"subp_id" => array ("type" => "string", "desc" => ""),
					"cms_password" => array ("type" => "string", "desc" => ""),
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_PARTNER ,
					APIErrors::PARTNER_REGISTRATION_ERROR , 
					
				)
			);
	}

	protected function ticketType()			{		return self::REQUIED_TICKET_NONE;	}

	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER; 	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}

	protected function allowEmptyPuser()	{		return true;	}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$partner = new Partner();
		$obj_wrapper = objectWrapperBase::getWrapperClass( $partner , 0 );

		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $partner , "partner_" , $obj_wrapper->getUpdateableFields() );
		// check that mandatory fields were set
		// TODO
		if ( count ( $fields_modified ) > 0 )
		{
			try
			{
				$cms_password = $this->getP ( "cms_password" );
				
				if ($cmsPassword) {
					if (!adminKuserPeer::isPasswordStructureValid($cmsPassword)) {
						$this->addError( APIErrors::PASSWORD_STRUCTURE_INVALID);
						return;
					}
				}
				
				$partner_registration = new myPartnerRegistration ();
				list($pid, $subpid, $pass, $hashKey) = $partner_registration->initNewPartner( $partner->getName() , $partner->getAdminName() , $partner->getAdminEmail() , $partner->getCommercialUse() ,
					"yes" , $partner->getDescription() , $partner->getUrl1() , $cms_password , $partner );

				$partner_from_db = PartnerPeer::retrieveByPK( $pid );

				// send a confirmation email as well as the result of the service
				$partner_registration->sendRegistrationInformationForPartner( $partner_from_db , $subpid , $pass, false, $hashKey);

			}
			catch ( SignupException $se )
			{
				$this->addError( APIErrors::PARTNER_REGISTRATION_ERROR , $se->getMessage() );
				return;
			}
			catch ( Exception $ex )
			{
				// this assumes the partner name is unique - TODO - remove key from DB !
				$this->addError( "Partner with name already exists" );
				$this->addError( $ex->getMessage() );
				return;
			}

			$this->addMsg ( "partner" , objectWrapperBase::getWrapperClass( $partner_from_db , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addMsg ( "subp_id" , $subpid );
			$this->addMsg ( "cms_password" , $pass );
			$this->addDebug ( "added_fields" , $fields_modified );

		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_PARTNER );
		}


	}
}
?>