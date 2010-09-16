<?php

class adddownloadAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "addDownload",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"entry_id" => array ("type" => "string", "desc" => ""),
						"file_format" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"entry_version" => array ("type" => "string", "desc" => ""),
						"conversion_quality" => array ("type" => "string", "desc" => ""),
						"force_download" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_ENTRY_ID,
					APIErrors::INVALID_ENTRY_VERSION,
					)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}

	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

    protected function getObjectPrefix()
    {
    	return "entry";
    }
    
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		KalturaLog::log("adddownloadAction: executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser)");
		
		$entry_id = $this->getPM ( "entry_id" );
		$version = $this->getP ( "version" );
		$file_format = strtolower($this->getPM ( "file_format" ));
		$conversion_quality = $this->getP ( "conversion_quality" , null );
		$force_download = $this->getP ( "force_download" , null );
		$entry = entryPeer::retrieveByPK( $entry_id );
		
		if ( ! $entry )
		{
			KalturaLog::log("add download Action entry not found");
			$this->addError ( APIErrors::INVALID_ENTRY_ID, $this->getObjectPrefix() , $entry_id );
			return;
		}
		
		KalturaLog::log("adddownloadAction: entry found [$entry_id]");
		
		/*			
		$content_path = myContentStorage::getFSContentRootPath();
		$file_name = $content_path . $entry->getDataPath( $version ); // replaced__getDataPath
		if (!file_exists($file_name))
		*/
		
		$sync_key = null;
		$originalFlavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entry->getId());
		if($originalFlavorAsset)
			$sync_key = $originalFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		if(!$sync_key)
			$sync_key = $entry->getSyncKey ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , $version );
		
		if ( ! kFileSyncUtils::file_exists( $sync_key ) )
		{
			// if not found local file - perhaps wasn't created here and wasn't synced yet
			// try to see if remote exists - and proxy the request if it is.
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($sync_key, true, true);
			if(!$local)
			{
				// take input params and add to URL
				$queryArr = array(
					'entry_id' => $entry_id,
					'version' => $version,
					'file_format' => $file_format,
					'conversion_quality' => $conversion_quality,
					'force_download' => $force_download,
					'ks' => $this->ks->toSecureString(),
					'partner_id' => $partner_id,
					'subp_id' => $subp_id,
					'format' => $this->response_type,
				);
				$get_query = http_build_query($queryArr, '', '&');
				$remote_url = kDataCenterMgr::getRedirectExternalUrl ( $fileSync , $_SERVER['REQUEST_URI'] );
				$url = (strpos($remote_url, '?') === FALSE)? $remote_url.'?'.$get_query: $remote_url.'&'.$get_query;
				// prxoy request to other DC
				KalturaLog::log ( __METHOD__ . ": redirecting to [$url]" );
				kFile::dumpUrl($url);
			}
			KalturaLog::log("add download Action sync key doesn't exists");
			$this->addError ( APIErrors::INVALID_ENTRY_VERSION, $this->getObjectPrefix(), $entry_id, $version );
			return; 
		}
		
		if ( $entry->getType() == entry::ENTRY_TYPE_SHOW  )
		{
			// TODO - should return the job ??
			// the original flavor should be considered as flv in case this is a roughcut
			if ( $file_format == "original" )	
				$file_format = "flv";
				
			$job = myBatchFlattenClient::addJob($puser_id, $entry, $version, $file_format);
			KalturaLog::log("add download Action flatten job [" . $job->getId() . "] created");
			
			return;
		}
		
		$flavorParamsId = 0;
		
		if ( $file_format != "original" )
		{
			if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO )
				$file_format = "flv";
				
			// Backward compatebility
			if ( !$file_format && $entry->getType() == entry::ENTRY_TYPE_DOCUMENT )
				$file_format = "swf";
				
			$flavorParams = myConversionProfileUtils::getFlavorParamsFromFileFormat ( $partner_id , $file_format );
			$flavorParamsId = $flavorParams->getId();
		}
		
		$job = kJobsManager::addBulkDownloadJob($partner_id, $puser_id, $entry->getId(), $flavorParamsId);
	
		// remove kConvertJobData object from batchJob.data
		$job->setData(null);
		$jobWrapperClass = objectWrapperBase::getWrapperClass($job, objectWrapperBase::DETAIL_LEVEL_DETAILED);
		$this->addMsg("download", $jobWrapperClass);

		// Backwards compatebilty for document entries 
		if ( $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_DOCUMENT || $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_PDF )
		{
			$this->addMsg("OOconvert", $jobWrapperClass);
			
			$download_path = $entry->getDownloadUrl();

			//TODO: once api_v3 will support parameters with '/' instead of '?', we can change this to war with api_v3
			$download_path .= '/direct_serve/true/type/download/forceproxy/true/format/'.$file_format;
			
			$this->addMsg('downloadUrl', $download_path);
		}
		
	}
}
?>
