<?php
/**
 * @package api
 * @subpackage ps2
 */
class updateentrymoderationAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateEntryTModeration",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"moderation_status" => array ("type" => "integer", "desc" => ""),
						),
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID ,
					APIErrors::INVALID_ENTRY_TYPE ,
				)
			); 
	}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		
		$entry_id = $this->getPM ( "entry_id" );
		$moderation_status = $this->getPM ( "moderation_status" );
		entryPeer::allowDeletedInCriteriaFilter();
		$entry = entryPeer::retrieveByPK( $entry_id );
		
		if ( $entry )
		{
			// when setting the moderation status- propagate to all related moderation objects
			$entry->moderate ( $moderation_status , true ); 
			$entry->setModerationCount ( 0 ); // set the number of unhandled flags to 0
			$entry->save();
	
			// for  no wodn't add an extra notification - one is sent from within the entry->moderate()
			// TODO - where is the best place to notify ??
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE , $entry, null, null, null, null, $entry_id );
		}
		
		entryPeer::blockDeletedInCriteriaFilter();
		
		$wrapper = objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_DETAILED );
		if ( $entry ) $wrapper->removeFromCache( "entry" , $entry->getId() );	
		
		$this->addMsg ( "entry" , $wrapper );
	}
}
?>