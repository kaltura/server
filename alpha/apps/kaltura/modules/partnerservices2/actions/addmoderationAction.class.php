<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class addmoderationAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addModeration",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"moderation" => array ("type" => "moderation", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"moderation" => array ("type" => "moderation", "desc" => "")
					),
				"errors" => array (
					APIErrors::MODERATION_OBJECT_NOT_EXISTS ,
				)
			); 
	}
	
	protected function ticketType()			{	return self::REQUIED_TICKET_ADMIN;	}

	public function needKuserFromPuser ( )	{	return self::KUSER_DATA_KUSER_ID_ONLY;	}
	
	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FORCE;	}

	protected function getStatusToUpdate ( $moderation = null ) 	
	{		
		if ( $moderation != null && $moderation->getStatus() ) return $moderation->getStatus();
		return moderation::MODERATION_STATUS_PENDING; 	
	}
	
	// will allow derived classes to alter the modification from received from the user 
	protected function fixModeration  ( moderation &$moderation ) {}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// get the new properties for the kuser from the request
		$moderation = new moderation();

		$obj_wrapper = objectWrapperBase::getWrapperClass( $moderation , 0 );

		// allow setting extended updateable fields
		$field_level = $this->isAdmin() ? 2 : 1;
		$updateable_fields = $obj_wrapper->getUpdateableFields( $field_level );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $moderation , "moderation_" , $updateable_fields );
		// check that mandatory fields were set
		// TODO

		$this->fixModeration ( $moderation );

		$moderation->setPartnerId( $partner_id );
		$moderation->setSubpId( $subp_id );
		$moderation->setPuserId( $puser_id );
		
		if ( count ( $fields_modified ) > 0 )
		{
			
			if ( $moderation->getObjectType() == moderation::MODERATION_OBJECT_TYPE_ENTRY )
			{
				$entry = $moderation->getObject() ;
				if ( !$entry )
				{
					$this->addError( APIErrors::MODERATION_OBJECT_NOT_EXISTS, $moderation->getObjectTypeAsString().":".$moderation->getObjectId());
					return;
				}
				else
				{
					$entry_moderation = $entry->getModerate();
					// avoid redundant save of entry
					if ( ! $entry_moderation )
					{
						$entry->setModerate ( true );
						$entry->save();
					}
				}
			}
			elseif ( $moderation->getObjectType() == moderation::MODERATION_OBJECT_TYPE_USER )
			{
				$user = $moderation->getObject() ;
				if ( !$user )
				{
					$this->addError( APIErrors::MODERATION_OBJECT_NOT_EXISTS, $moderation->getObjectTypeAsString().":".$moderation->getObjectId());
					return;
				}
			}
			else
			{
				$this->addError( APIErrors::MODERATION_ONLY_ENTRY, $moderation->getObjectTypeAsString() );
				return;
			}
			
// remove - there can be many moderations per object
/*			
			$moderation_from_db = moderationPeer::getByStatusAndObject( moderation::MODERATION_STATUS_PENDING  , $moderation->getObjectId() , $moderation->getObjectType() );
			if ( $moderation_from_db )
			{
				$this->addMsg ( "moderation" , objectWrapperBase::getWrapperClass( $moderation_from_db , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
				$this->addDebug ( "already_moderated" , "1");
				return;				
			}
	*/		
			// TODO - decide how to describe the subject of the moderation
//			$moderation->setPuserId( $puser_id );
//			$moderation->setKuserId( $puser_kuser->getKuserId() );
//			$moderation->setStatus( moderation::MODERATION_STATUS_PENDING )
			$new_status = $this->getStatusToUpdate( $moderation );
		
			$moderation->updateStatus( $new_status );
			$moderation->save();
				
			$this->addMsg ( "moderation" , objectWrapperBase::getWrapperClass( $moderation , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addDebug ( "added_fields" , $fields_modified );
				
		}
		else
		{
			$this->addError( APIErrors::MODERATION_EMPTY_OBJECT );
		}


	}
}
?>