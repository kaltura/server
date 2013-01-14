<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveFlavorAction extends kalturaAction
{
	const CHUNK_SIZE = 1048576; // 1024 X 1024
	
	public function execute()
	{
		//entitlement should be disabled to serveFlavor action as we do not get ks on this action.
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		requestUtils::handleConditionalGet();

		$flavorId = $this->getRequestParameter("flavorId");
		$shouldProxy = $this->getRequestParameter("forceproxy", false);
		$ks = $this->getRequestParameter( "ks" );
		$fileParam = $this->getRequestParameter( "file" );
		$fileParam = basename($fileParam);
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';
	
		$flavorAsset = assetPeer::retrieveById($flavorId);	
		if (is_null($flavorAsset))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$entryId = $this->getRequestParameter("entryId");
		if (!is_null($entryId) && $flavorAsset->getEntryId() != $entryId)
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		
		$clipTo = null;
//		$securyEntryHelper = new KSecureEntryHelper($entry, $ks, $referrer, accessControlContextType::PLAY);
//		if ($securyEntryHelper->shouldPreview())
//		{
//			$clipTo = $securyEntryHelper->getPreviewLength() * 1000;
//		}
//		else
//		{
//			$securyEntryHelper->validateForPlay($entry, $ks);
//		}
			
		myPartnerUtils::blockInactivePartner($flavorAsset->getPartnerId());
		myPartnerUtils::enforceDelivery($flavorAsset->getPartnerId());
		
		//disabled enforce cdn because of rtmp delivery
		//requestUtils::enforceCdnDelivery($flavorAsset->getPartnerId());
			
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if (!kFileSyncUtils::file_exists($syncKey, false))
		{
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			
			if (is_null($fileSync))
			{
				KalturaLog::log("Error - no FileSync for flavor [".$flavorAsset->getId()."]");
				KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			}
			
			// always dump remote urls so they will be cached by the cdn transparently
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			kFileUtils::dumpUrl($remoteUrl);
		}
		
		$path = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
	
		$isFlv = false;
		if (!$shouldProxy) // if the forceproxy is set dump file and dont treat it as flv (for progressive download)
		{
			$flvWrapper = new myFlvHandler ( $path );
			$isFlv = $flvWrapper->isFlv();
		}
	
	
		$clipFrom = $this->getRequestParameter ( "clipFrom" , 0); // milliseconds
		if(is_null($clipTo))
			$clipTo = $this->getRequestParameter ( "clipTo" , 2147483647 ); // milliseconds
		if($clipTo == 0) 
			$clipTo = 2147483647;


		if($fileParam && is_dir($path)) {
			$path .= "/$fileParam";
			kFileUtils::dumpFile($path, null, null);
			KExternalErrors::dieGracefully();
		}
		else if (!$isFlv) // dump as regular file if the forceproxy parameter was specified or the file isn't an flv
		{
			$limit_file_size = 0;
			if ($clipTo != 2147483647)
			{
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
				if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
				{
					$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : ($mediaInfo->getAudioDuration() ?
					$mediaInfo->getAudioDuration() : $mediaInfo->getContainerDuration()));
					$limit_file_size = floor(@kFile::fileSize($path) * ($clipTo / $duration));
				}
			}
			kFileUtils::dumpFile($path, null, null, $limit_file_size);
			KExternalErrors::dieGracefully();
		}
		
		$audioOnly = $this->getRequestParameter ( "audioOnly" ); // milliseconds
		if ( $audioOnly === '0' )
		{
			// audioOnly was explicitly set to 0 - don't attempt to make further automatic investigations
		}
		elseif ( $flvWrapper->getFirstVideoTimestamp() < 0 )
		{
			$audioOnly = true; 
		}
		
		$seekFrom = $this->getRequestParameter ( "seekFrom" , -1);
		if ($seekFrom <= 0)
			$seekFrom = -1;
		
		$seekFromBytes = $this->getRequestParameter ( "seekFromBytes" , -1);
		if ($seekFromBytes <= 0)
			$seekFromBytes = -1;
	
		$bytes = 0;
		if ($seekFrom !== -1 && $seekFrom !== 0)
		{
			list ( $bytes , $duration ,$firstTagByte , $toByte ) = $flvWrapper->clip(0, -1, $audioOnly );
			list ( $bytes , $duration ,$fromByte , $toByte, $seekFromTimestamp ) = $flvWrapper->clip($seekFrom, -1, $audioOnly );
			$seekFromBytes = myFlvHandler::FLV_HEADER_SIZE + $flvWrapper->getMetadataSize( $audioOnly  ) + $fromByte - $firstTagByte;
		}
		else
		{		
			list ( $bytes , $duration ,$fromByte , $toByte, $fromTs, $cuepointPos) = myFlvStaticHandler::clip($path , $clipFrom , $clipTo, $audioOnly );
		}
		
		$metadataSize = $flvWrapper->getMetadataSize( $audioOnly );
		$dataOffset = $metadataSize + myFlvHandler::getHeaderSize();
		$totalLength = $dataOffset + $bytes;
		
		list ( $bytes , $duration ,$fromByte , $toByte, $fromTs, $cuepointPos) = myFlvStaticHandler::clip($path , $clipFrom , $clipTo, $audioOnly );
		list($rangeFrom, $rangeTo, $rangeLength) = requestUtils::handleRangeRequest($totalLength);

		if ($totalLength < 1000) // (actually $total_length is probably 13 or 143 - header + empty metadata tag) probably a bad flv maybe only the header - dont cache
			requestUtils::sendCdnHeaders("flv", $rangeLength, 0);
		else
			requestUtils::sendCdnHeaders("flv", $rangeLength);
			
		header('Content-Disposition: attachment; filename="video.flv"');
				
		// dont inject cuepoint into the stream
		$cuepointTime = 0;
		$cuepointPos = 0;
				
		try
		{
			Propel::close();
		}
		catch(Exception $e)
		{
			$this->logMessage( "serveFlavor: error closing db $e");
		}
		
		header("Content-Type: video/x-flv");
		$flvWrapper->dump(self::CHUNK_SIZE, $fromByte, $toByte, $audioOnly, $seekFromBytes, $rangeFrom, $rangeTo, $cuepointTime, $cuepointPos);
		KExternalErrors::dieGracefully();
	}
}
