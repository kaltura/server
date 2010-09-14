<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");
require_once ( "myPartnerRegistration.class.php");

class updatepartnerAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "updatePartner",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"partner" => array ("type" => "Partner", "desc" => ""),
						),
					"optional" => array (

						)
					),
				"out" => array (
					"partner" => array ("type" => "Partner", "desc" => ""),
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
	
	const KALTURAS_PARTNER_EMAIL_CHANGE = 52;
		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$allow_empty = $this->getP ( "allow_empty_field" , false );
		if ( $allow_empty == "false" || $allow_empty === 0 ) $allow_empty = false;
		
		
		$partner = new Partner();
		$obj_wrapper = objectWrapperBase::getWrapperClass( $partner , 0 );

		$updateable_fields = $obj_wrapper->getUpdateableFields() ;
		
		// TODO - use fillObjectFromMapOrderedByFields instead 
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $partner , "partner_" , 
			$updateable_fields , BasePeer::TYPE_PHPNAME ,$allow_empty );
		// check that mandatory fields were set
		// TODO
		if ( count ( $fields_modified ) > 0 )
		{
			$target_partner = PartnerPeer::retrieveByPK( $partner_id );
			if ( $partner && $target_partner )
			{
				if ( @$fields_modified["adminEmail"] && $target_partner->getAdminEmail() != $fields_modified["adminEmail"]) {
					myPartnerUtils::emailChangedEmail($partner_id, $target_partner->getAdminEmail(), $fields_modified["adminEmail"], $target_partner->getName(), updatepartnerAction::KALTURAS_PARTNER_EMAIL_CHANGE);
				}
				$partner->setType ( $target_partner->getType() );
				baseObjectUtils::fillObjectFromObject( $updateable_fields , $partner , $target_partner , 
					baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME ,$allow_empty );
				
				$target_partner->save();
				$this->addMsg ( "partner" , objectWrapperBase::getWrapperClass( $target_partner , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
				$this->addDebug ( "added_fields" , $fields_modified );
			}
			else
			{
				$this->addError( APIErrors::UNKNOWN_PARTNER_ID );
			}
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_PARTNER );
		}

	}
}
?>