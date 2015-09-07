<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveFlavorAction extends kalturaAction
{
	const CHUNK_SIZE = 1048576; // 1024 X 1024
	const NO_CLIP_TO = 2147483647;
	
	protected function storeCache($renderer, $partnerId)
	{
		if (!function_exists('apc_store') || $_SERVER["REQUEST_METHOD"] != "GET")
		{
			return;
		}

		$renderer->partnerId = $partnerId;
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
		$cacheKey = 'dumpFile-'.kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']).'-'.$host.$_SERVER["REQUEST_URI"];
		apc_store($cacheKey, $renderer, 86400);
		header("X-Kaltura:cache-key");
	}
	
	public function execute()
	{
		//entitlement should be disabled to serveFlavor action as we do not get ks on this action.
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		requestUtils::handleConditionalGet();

		$flavorId = $this->getRequestParameter("flavorId");
		$shouldProxy = $this->getRequestParameter("forceproxy", false);
		$fileName = $this->getRequestParameter( "fileName" );
		$fileParam = $this->getRequestParameter( "file" );
		$fileParam = basename($fileParam);
		$pathOnly = $this->getRequestParameter( "pathOnly", false );
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';

		$flavorAsset = assetPeer::retrieveById($flavorId);	
		if (is_null($flavorAsset))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$entryId = $this->getRequestParameter("entryId");
		if (!is_null($entryId) && $flavorAsset->getEntryId() != $entryId)
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		if ($fileName)
		{
			header("Content-Disposition: attachment; filename=\"$fileName\"");
			header("Content-Type: application/force-download");
			header( "Content-Description: File Transfer" );
		}

		$clipTo = null;
		
		$entry = $flavorAsset->getentry();
		if (!$entry)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
		
		KalturaMonitorClient::initApiMonitor(false, 'extwidget.serveFlavor', $flavorAsset->getPartnerId());
			
		myPartnerUtils::enforceDelivery($entry, $flavorAsset);
		
		$version = $this->getRequestParameter( "v" );
		if (!$version)
			$version = $flavorAsset->getVersion();
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $version);

		if ($pathOnly && kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']))
		{
			$path = null;
			list ( $file_sync , $local )= kFileSyncUtils::getReadyFileSyncForKey( $syncKey , false, false );
			if ( $file_sync )
			{
				$parent_file_sync = kFileSyncUtils::resolve($file_sync);
				$path = $parent_file_sync->getFullPath();
				if ($fileParam && is_dir($path)) 
				{
					$path .= "/$fileParam";
				}
			}
		
			$renderer = new kRendererString(
				'<?xml version="1.0" encoding="utf-8"?><xml><result>' . $path . '</result></xml>', 
				'text/xml');
			if ($path)
			{
				$this->storeCache($renderer, $flavorAsset->getPartnerId());
			}
			$renderer->output();
			KExternalErrors::dieGracefully();
		}
		
		if (kConf::hasParam('serve_flavor_allowed_partners') && 
			!in_array($flavorAsset->getPartnerId(), kConf::get('serve_flavor_allowed_partners')))
		{
			KExternalErrors::dieError(KExternalErrors::ACTION_BLOCKED);
		}

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
			$clipTo = $this->getRequestParameter ( "clipTo" , self::NO_CLIP_TO ); // milliseconds
		if($clipTo == 0) 
			$clipTo = self::NO_CLIP_TO;
		if(!is_numeric($clipTo) || $clipTo < 0)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'clipTo must be a positive number');
		
		$seekFrom = $this->getRequestParameter ( "seekFrom" , -1);
		if ($seekFrom <= 0)
			$seekFrom = -1;
		
		$seekFromBytes = $this->getRequestParameter ( "seekFromBytes" , -1);
		if ($seekFromBytes <= 0)
			$seekFromBytes = -1;
		
		
		if($fileParam && is_dir($path)) {
			$path .= "/$fileParam";
			kFileUtils::dumpFile($path, null, null);
			KExternalErrors::dieGracefully();
		}
		else if (!$isFlv || ($clipTo == self::NO_CLIP_TO && $seekFrom < 0 && $seekFromBytes < 0)) // dump as regular file if the forceproxy parameter was specified or the file isn't an flv
		{
			$limit_file_size = 0;
			if ($clipTo != self::NO_CLIP_TO)
			{
				if (strtolower($flavorAsset->getFileExt()) == 'mp4' && 
					PermissionPeer::isValidForPartner(PermissionName::FEATURE_ACCURATE_SERVE_CLIPPING, $flavorAsset->getPartnerId()))
				{
					$contentPath = myContentStorage::getFSContentRootPath();
					$tempClipName = $version . '_' . $clipTo . '.mp4';
					$tempClipPath = $contentPath . myContentStorage::getGeneralEntityPath("entry/tempclip", $flavorAsset->getIntId(), $flavorAsset->getId(), $tempClipName);
					if (!file_exists($tempClipPath))
					{
						kFile::fullMkdir($tempClipPath);
						$clipToSec = round($clipTo / 1000, 3);
						$cmdLine = kConf::get ( "bin_path_ffmpeg" ) . " -i {$path} -vcodec copy -acodec copy -f mp4 -t {$clipToSec} -y {$tempClipPath} 2>&1";
						KalturaLog::log("Executing {$cmdLine}");
						$output = array ();
						$return_value = "";
						exec($cmdLine, $output, $return_value);
						KalturaLog::log("ffmpeg returned {$return_value}, output:".implode("\n", $output));
					}
					
					if (file_exists($tempClipPath))
					{
						KalturaLog::log("Dumping {$tempClipPath}");
						kFileUtils::dumpFile($tempClipPath);
					}
					else
					{
						KalturaLog::err('Failed to clip the file using ffmpeg, falling back to rough clipping');
					}
				}
				
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
				if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
				{
					$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : ($mediaInfo->getAudioDuration() ?
					$mediaInfo->getAudioDuration() : $mediaInfo->getContainerDuration()));
					$limit_file_size = floor((@kFile::fileSize($path) * ($clipTo / $duration))*1.2);
				}
			}
			
			$renderer = kFileUtils::getDumpFileRenderer($path, null, null, $limit_file_size);
			if(!$fileName)
				$this->storeCache($renderer, $flavorAsset->getPartnerId());
			$renderer->output();
			
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
