<?php
/**
 * @package api
 * @subpackage ps2
 */
class updateentryAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateEntry",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"entry" => array ("type" => "entry", "desc" => "")
						),
					"optional" => array (
						"allow_empty_field" => array ( "type" => "boolean" , "desc" => "" )
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID ,
					APIErrors::INVALID_ENTRY_ID ,
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY;	}
	
	public function requiredPrivileges () { return "edit:<kshow_id>" ; }

	public function verifyEntryPrivileges ( $entry ) 
	{
		if($entry->getKshowId())
		{
			$priv_id = $entry->getKshowId();
		}
		else
		{
			$priv_id = $entry->getId();
		}
		return $this->verifyPrivileges ( "edit" , $priv_id ); // user was granted explicit permissions when initiatd the ks
	}
	
	protected function getObjectPrefix () { return "entry"; } // TODO - fix to be entries

	protected function validateInputEntry ( $entry ) {}
	protected function validateEntry ( $entry ) {} 
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$prefix = $this->getObjectPrefix();
		
		if ( ! $puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID ,$puser_id );
			return;
		}

		$allow_empty = $this->getP ( "allow_empty_field" , false );
		if ( $allow_empty == "false" || $allow_empty === 0 ) $allow_empty = false;
		
		$entry_id = $this->getPM ( "{$prefix}_id" );
		$entry = entryPeer::retrieveByPK( $entry_id );

		if ( ! $entry )  
		{
			$this->addError( APIErrors::INVALID_ENTRY_ID , $prefix , $entry_id );
			return;
		}			
		
		$this->validateInputEntry( $entry );
		
		// TODO - verify the user is allowed to modify the entry
		if ( ! $this->isOwnedBy ( $entry , $puser_kuser->getKuserId() ) )
		{
			$this->verifyEntryPrivileges ( $entry ); // user was granted explicit permissions when initiatd the ks
		}
		
		// get the new properties for the kuser from the request
		$entry_update_data = new entry();
		
		// assume the type and media_type of the entry from the DB are the same as those of the one from the user - if not -they will be overriden
		$entry_update_data->setType ( $entry->getType() );
		$entry_update_data->setMediaType ( $entry->getMediaType() );
		$entry_update_data->setId ( $entry->getId() );
		$entry_update_data->setPartnerId ( $entry->getPartnerId() );
		$entry_update_data->setData ( $entry->getData() , true );
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $entry_update_data , 0 );
		
		$field_level = $this->isAdmin() ? 2 : 1;
		$updateable_fields = $obj_wrapper->getUpdateableFields( $field_level );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $entry_update_data , "{$prefix}_" , $updateable_fields ,
			BasePeer::TYPE_PHPNAME , $allow_empty );
		if ( count ( $fields_modified ) > 0 )
		{
			if ( $entry_update_data )
			{
				// allow admins to set admin more fields
				baseObjectUtils::fillObjectFromObject( $updateable_fields , $entry_update_data , $entry , 
					baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME , $allow_empty );
			}

			$this->validateEntry ( $entry );
			// TODO - chack to see that the permissions changed, not just any attributes
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_PERMISSIONS, $entry );
				
			$entry->save();
		}
		
		$wrapper = objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_DETAILED );
		$wrapper->removeFromCache( "entry" , $entry->getId() );			
		
		$this->addMsg ( "{$prefix}" , $wrapper );
		$this->addDebug ( "modified_fields" , $fields_modified );

	}
}
?>