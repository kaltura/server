<?php
/**
 * @package api
 * @subpackage ps2
 */
class updateentriesthumbnailsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateEntriesThumbnails",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_ids" => array ("type" => "integer", "desc" => ""),
						"time_offset" => array ("type" => "integer", "desc" => "") ,
						),
					"optional" => array (
						)
					),
				"out" => array (
					"entries" => array ("type" => "*entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID ,
					APIErrors::INVALID_ENTRY_TYPE ,
				)
			); 
	}
	
	protected function ticketType ()	{		return self::REQUIED_TICKET_ADMIN;	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_DATA;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		if ( ! $puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID ,$puser_id );
			return;
		}
		
		$time_offset = $this->getPM ( "time_offset" );
		$entry_ids = $this->getPM ( "entry_ids" );
		$detailed = $this->getP ( "detailed" , false );
		$separator = $this->getP ( "separator" , "," );

		$id_arr = explode ( $separator , $entry_ids );
		$limit = 50;
		$id_arr = array_splice( $id_arr , 0 , $limit );

		$entries = entryPeer::retrieveByPKs( $id_arr );
		$updated_entries = array();
		
		if ( ! $entries )
		{
			$this->addError ( APIErrors::INVALID_ENTRY_IDS , $entry_ids);
		}
		else
		{
			foreach ( $entries as $entry )
			{
				if (!myEntryUtils::createThumbnailFromEntry($entry, $entry, $time_offset))
				{
					$this->addError ( APIErrors::INVALID_ENTRY_TYPE , "ENTRY_TYPE_MEDIACLIP ["  . $entry->getId() . "]" );
					continue;
				}
		
				$updated_entries[] =$entry ;
				myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL , $entry );
				
				$wrapper = objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_DETAILED );
				$wrapper->removeFromCache( "entry" , $entry->getId() );	
			}
		}
		
		$this->addMsg ( "entries" , objectWrapperBase::getWrapperClass( $updated_entries , objectWrapperBase::DETAIL_LEVEL_REGULAR ) );
	}
}
?>