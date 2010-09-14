<?php
require_once ( "defPartnerservices2Action.class.php");

class getmetadataAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getMetaDataAction",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"kshow_id" => array ("type" => "string", "desc" => ""),
						"version"  => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"metadata" => array ("type" => "xml", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID , 
					APIErrors::INVALID_ENTRY_ID ,
					APIErrors::INVALID_FILE_NAME , 
				)
			); 		
	}
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$entry_id = $this->getP ( "entry_id" );
		$kshow_id =  $this->getP ( "kshow_id" );
		
		// Make sure the request is for a ready roughcut
		$c = entryPeer::getCriteriaFilter()->getFilter();
		$c->addAnd ( entryPeer::STATUS, entry::ENTRY_STATUS_READY , Criteria::EQUAL);
				
		list ( $kshow , $entry , $error , $error_obj ) = myKshowUtils::getKshowAndEntry( $kshow_id  , $entry_id );

		if ( $error_obj )
		{
			$this->addError ( $error_obj );
			return ;
		}

		$version = $this->getP ( "version" ); // it's a path on the disk
		if ( kString::beginsWith( $version , "." ) )
		{
			// someone is trying to hack in the system 
			return sfView::ERROR;	
		}
		elseif ( $version == "-1" ) $version = null;
				
			// in case we're making a roughcut out of a regular invite, we start from scratch
		$entry_data_path = kFileSyncUtils::getLocalFilePathForKey($entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA, $version)); //replaced__getDataPath
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_SHOW || $entry_data_path === null)
		{
			$this->xml_content = "<xml></xml>"; 
			return;
		}

		$sync_key = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , $version );
		$file_name = kFileSyncUtils::getReadyLocalFilePathForKey( $sync_key , false );
			
		// fetch content of file from disk - it should hold the XML
		if ( kString::endsWith( $file_name  , "xml" ))
		{
			$xml_content = kFileSyncUtils::file_get_contents( $sync_key , false  , false );
			if ( ! $xml_content)
			{
				$xml_content = "<xml></xml>"; 
			}
			myMetadataUtils::updateEntryForPending( $entry , $version , $xml_content );
			$this->addMsg ( "metadata" , $xml_content );
		}
		else
		{
			$this->addError( APIErrors::INVALID_FILE_NAME , $file_name );
		}
				
		

	}
}
?>