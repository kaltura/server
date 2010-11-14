<?php
require_once ( "defPartnerservices2Action.class.php");

class updateentrythumbnailAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateEntryThumbnail",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"source_entry_id" => array ("type" => "string", "desc" => ""),				
						"time_offset" => array ("type" => "integer", "desc" => "")
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID ,
					APIErrors::INVALID_ENTRY_TYPE ,
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_DATA;
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		if ( ! $puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID ,$puser_id );
			return;
		}
		
		$entry_id = $this->getPM ( "entry_id" );
		$entry = entryPeer::retrieveByPK( $entry_id );
		
		// TODO - verify the user is allowed to modify the entry
		
		$source_entry_id = $this->getP ( "source_entry_id" );
		
		if ( $source_entry_id )
		{
			$source_entry = entryPeer::retrieveByPK($source_entry_id);
			if (!$source_entry)
				return;
		}
		else
			$source_entry = $entry;
		
		$time_offset = $this->getP ( "time_offset", -1);
		
		if (!myEntryUtils::createThumbnailFromEntry($entry, $source_entry, $time_offset))
		{
			$this->addError ( APIErrors::INVALID_ENTRY_TYPE , "ENTRY_TYPE_MEDIACLIP" );
			return;
		}
		
		if ($entry->getType() == entryType::MIX)
		{
/*			
			$roughcutPath = myContentStorage::getFSContentRootPath() . $entry->getDataPath(); // replaced__getDataPath
			$xml_doc = new DOMDocument();
			$xml_doc->load( $roughcutPath );
		
			if (myMetadataUtils::updateThumbUrl($xml_doc, $entry->getThumbnailUrl()))
				$xml_doc->save($roughcutPath);
	*/
			$sync_key = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA );
			$xml_doc = new DOMDocument();
			$xml_doc->loadXML( kFileSyncUtils::file_get_contents( $sync_key ) );
			if (myMetadataUtils::updateThumbUrl($xml_doc, $entry->getThumbnailUrl()))
			{
				$entry->setMetadata ( null , $xml_doc->saveXML( ) , true , null ,  null ) ;//$entry->getVersion() );
			}
			
			myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE_THUMBNAIL , $entry );
		}
		
		$wrapper = objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_DETAILED );
		$wrapper->removeFromCache( "entry" , $entry->getId() );	
		
		$this->addMsg ( "entry" , $wrapper );
	}
}
?>