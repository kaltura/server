<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveFlavorAction extends kalturaAction
{
	const CHUNK_SIZE = 1048576; // 1024 X 1024
	const NO_CLIP_TO = 2147483647;
	
	const JSON_CONTENT_TYPE = 'application/json';
	const TYPE_SOURCE = 'source';
	const PATH_EMPTY = 'empty';

	const SECOND_IN_MILLISECONDS = 1000;
	const TIME_MARGIN = 10000; // 10 seconds in milliseconds. a safety margin to compensate for clock differences

	protected $pathOnly = false;
	protected static $requestAuthorized = false;
	protected static $preferredStorageId = null;
	protected static $fallbackStorageId = null;

	protected static function jsonEncode($obj)
	{
		$options = 0;
		if (defined('JSON_UNESCAPED_UNICODE'))
		{
			$options |= JSON_UNESCAPED_UNICODE;
		}
		return json_encode($obj, $options);
	}

	protected function storeCache($renderer, $partnerId)
	{
		if (!function_exists('apc_store') || 
			$_SERVER["REQUEST_METHOD"] != "GET" || 
			$renderer instanceof kRendererString)
		{
			return;
		}

		$renderer->partnerId = $partnerId;
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
		$cacheKey = 'dumpFile-'.kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']).'-'.$host.$_SERVER["REQUEST_URI"];
		apc_store($cacheKey, $renderer, 86400);
		header("X-Kaltura:cache-key");
	}
	protected function getSimpleMappingRenderer($path, asset $asset = null, FileSync $fileSync = null, $sourceType = kFileSyncUtils::SOURCE_TYPE_FILE)
	{
		$source = self::getAssetFieldsArray(self::TYPE_SOURCE, $path, $sourceType);

		if ($asset && $asset->getEncryptionKey())
		{
			$source['encryptionKey'] = $asset->getEncryptionKey();
		}
		else if ($fileSync && $fileSync->getEncryptionKey())
		{
			$encryptionKey = $fileSync->getEncryptionKey();
			$encryptionKey = substr($encryptionKey, 0, 32);
			$encryptionKey .= str_repeat("\0", 32 - strlen($encryptionKey));
			$source['encryptionKey'] = base64_encode($encryptionKey);
			$source['encryptionIv'] = base64_encode(kConf::get("encryption_iv"));
			$source['encryptionScheme'] = 'aes-cbc';
		}
		
		$sequence = array(
			'clips' => array($source)
		);

		if ($asset && method_exists($asset, 'getLanguage') && $asset->getLanguage())
		{
			$language = languageCodeManager::getObjectFromKalturaName($asset->getLanguage());
			$language = $language[1];
			
			// map enu / enb to eng, since these are not supported by the packager 
			if ($language == 'enu' || $language == 'enb')
			{
				$language = 'eng';
			} 
			
			if ($language && $language != 'und')
			{
				$sequence['language'] = $language;		// ISO639_T
			}
		}

		if ($asset && method_exists($asset, 'getLabel') && $asset->getLabel())
		{
			$sequence['label'] = $asset->getLabel();
		}
		
		$result = array(
			'sequences' => array($sequence)
		);

		$noCachePattern = kConf::get('serve_flavor_no_cache_pattern', 'local', '');
		if ($asset && in_array($asset->getType(), assetPeer::retrieveAllFlavorsTypes()) && $noCachePattern && preg_match($noCachePattern, $path))
		{
			$result['cache'] = false;
		}

		$json = str_replace('\/', '/', self::jsonEncode($result));

		return new kRendererString(
				$json,
				self::JSON_CONTENT_TYPE);
	}

	/**
	 * This will make nginx-vod dump the request to the remote dc
	 */
	protected function renderEmptySimpleMapping()
	{
		if (!$this->pathOnly || (!self::$requestAuthorized))
			return;

		$renderer = $this->getSimpleMappingRenderer('', null);
		$renderer->output();
	}

	public static function serveLiveMediaSet($durations, $sequences, $playlistStartTime = 1451624400000,
										 $firstClipStartTime, $initialClipIndex, $initialSegmentIndex,
										 $repeat, $discontinuity, $dvrWindow = null, $endTime = null)
	{
		$mediaSet['playlistType'] = 'live';
		$mediaSet['firstClipTime'] = $firstClipStartTime;
		$mediaSet['discontinuity'] = $discontinuity;

		if (!is_null($endTime))
		{
			$mediaSet['presentationEndTime'] = $endTime;
		}

		if($repeat)
		{
			$mediaSet['segmentBaseTime'] = (int)$playlistStartTime;
		}
		else
		{
			$mediaSet['initialClipIndex'] = $initialClipIndex;
			$mediaSet['initialSegmentIndex'] = $initialSegmentIndex;
		}

		$mediaSet['durations'] = $durations;
		$mediaSet['sequences'] = $sequences;
		if(!is_null($dvrWindow))
		{
			$mediaSet['liveWindowDuration'] = $dvrWindow;
		}

		return $mediaSet;
	}
	
	protected function servePlaylist($entry, $captionLanguages)
	{
		// allow only manual playlist
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_TEXT)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}

		$isLive = $this->getRequestParameter("live");

		$version = $this->getRequestParameter("v");
		self::$preferredStorageId = $this->getRequestParameter('preferredStorageId');
		self::$fallbackStorageId = $this->getRequestParameter('fallbackStorageId');

		// execute the playlist
		if ($version)
		{
			$entry->setDesiredVersion($version);
		}
		
		list($entryIds, $durations, $referenceEntry, $captionFiles) = myPlaylistUtils::executeStitchedPlaylist($entry, $captionLanguages);
		$this->serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $entry, null,
			$captionFiles, $captionLanguages, $isLive, 0, 0, 0, 0);
	}

	protected function serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $origEntry, $flavorParamIds,
	                                          $captionFiles, $captionLanguages, $isLive,
	                                          $playlistStartTime, $firstClipStartTime, $initialClipIndex, $initialSegmentIndex)
	{
		// get request parameters
		if (!$flavorParamIds)
		{
			$flavorParamIds = $this->getRequestParameter("flavorParamIds");
			if ($flavorParamIds)
			{
				$flavorParamIds = explode(',', $flavorParamIds);
			}
		}

		if (!$referenceEntry)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}

		// load the flavor assets
		// Note: not filtering by $flavorParamIds here, so that in case some flavor is missing
		//		we can fill in the gap using some other flavor params
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryIds, Criteria::IN);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);
		$flavorTypes = assetPeer::retrieveAllFlavorsTypes();
		$c->add(assetPeer::TYPE, $flavorTypes, Criteria::IN);
		$flavorAssets = assetPeer::doSelect($c);

		// group the flavors by entry and flavor params
		$groupedFlavors = array();
		foreach ($flavorAssets as $flavor)
		{
			if (!isset($groupedFlavors[$flavor->getEntryId()]))
			{
				$groupedFlavors[$flavor->getEntryId()] = array();
			}
			$groupedFlavors[$flavor->getEntryId()][$flavor->getFlavorParamsId()] = $flavor;
		}

		// remove entries that don't have flavors
		for ($i = count($entryIds) - 1; $i >= 0; $i--)
		{
			$entryId = $entryIds[$i];
			if (isset($groupedFlavors[$entryId]))
			{
				continue;
			}

			unset($entryIds[$i]);
			unset($durations[$i]);
		}
		$durations = array_values($durations);		// if some duration was unset, this makes sure that durations will be rendered as an array in the json

		// get the flavor params of the reference entry that should be returned
		$referenceEntryFlavorParamsIds = array_keys($groupedFlavors[$referenceEntry->getId()]);

		if ($flavorParamIds)
		{
			$flavorParamIds = array_intersect($referenceEntryFlavorParamsIds, $flavorParamIds);
		}
		else
		{
			$flavorParamIds = $referenceEntryFlavorParamsIds;
		}

		if (!$flavorParamIds)
		{
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		}

		// build the sequences
		$storeCache = true;
		$sequences = array();
		if ($this->getRequestParameter("flavorParamIds") || !$this->getRequestParameter("captions") || !($origEntry->getType() == entryType::PLAYLIST)) {
			foreach ($flavorParamIds as $flavorParamsId)
			{
				$referenceFlavor = $groupedFlavors[$referenceEntry->getId()][$flavorParamsId];
				$origEntryFlavor = $referenceFlavor;
				// build the clips of the current sequence
				$clips = array();
				foreach ($entryIds as $entryId)
				{
					if (isset($groupedFlavors[$entryId][$flavorParamsId]))
					{
						$flavor = $groupedFlavors[$entryId][$flavorParamsId];
					}
					else
					{
						$flavor = $this->getBestMatchFlavor($groupedFlavors[$entryId], $referenceFlavor);
					}

					if ($flavor->getEntryId() == $origEntry->getId())
					{
						$origEntryFlavor = $flavor;
					}
					// get the file path of the flavor
					$syncKey = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

					list ($file_sync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($syncKey, $flavor, self::getPreferredStorageProfileId(), self::getFallbackStorageProfileId(), $this->pathOnly);

					if(!$file_sync)
					{
						KalturaLog::debug('missing file sync for flavor ' . $flavor->getId() . ' version ' . $flavor->getVersion());
						$path = '';
						$storeCache = false;
					}

					$clips[] = self::getClipData($path, $flavor, $sourceType);
				}
				$sequences[] = array('clips' => $clips, 'id' => $this->getServeUrlForFlavor($origEntryFlavor->getId(), $origEntry->getId()));
			}
		}
		if ($captionFiles)
			$this->addCaptionSequences($entryIds, $captionFiles, $captionLanguages, $sequences, $origEntry);

		// build the media set
		if ($isLive)
		{
			$repeat = $origEntry->getRepeat() ? true : false;
			$mediaSet = self::serveLiveMediaSet($durations, $sequences,
				$playlistStartTime, $firstClipStartTime, $initialClipIndex, $initialSegmentIndex, $repeat, !$repeat);
		}
		else
		{
			$mediaSet = array('durations' => $durations, 'sequences' => $sequences);
		}

		$this->sendJson($mediaSet, $storeCache, $isLive, $origEntry);
	}

	protected function sendJson($jsonArray, $storeCache, $isLive, $entry)
	{
		// build the json
		$json = self::jsonEncode($jsonArray);
		$renderer = new kRendererString($json, self::JSON_CONTENT_TYPE);
		if ($storeCache && !$isLive)
		{
			$this->storeCache($renderer, $entry->getPartnerId());
		}

		$renderer->output();
		KExternalErrors::dieGracefully();
	}

	protected function serveEntryWithSequence($entry, $sequenceEntries, $asset, $flavorParamId, $captionLanguages)
	{
		/* @var asset $asset */
		$allEntries = $sequenceEntries;
		$allEntries[] = $entry;
		if (empty($captionLanguages) && $asset && $asset->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			$captionLanguages = $asset->getLanguage();
		$flavorParamsIdsArr = null;
		if ($flavorParamId)
			$flavorParamsIdsArr = array($flavorParamId);
		list($entryIds, $durations, $referenceEntry, $captionFiles ) =
			myPlaylistUtils::getPlaylistDataFromEntries($allEntries, $flavorParamsIdsArr, $captionLanguages);

		if ($asset && $asset->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
		{
			$this->serveCaptionsWithSequence($entryIds, $captionFiles, $durations, $captionLanguages, $entry->getPartnerId(), $entry);
		}

		$isLive = $this->getRequestParameter("live");

		$this->serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $entry, $flavorParamsIdsArr,
			$captionFiles, $captionLanguages, $isLive, 0, 0, 0, 0);
	}

	protected function serveCaptionsWithSequence($entryIds, $captionFiles, $durations, $captionLangauges, $partnerId, $mainEntry)
	{
		$sequences = array();

		$this->addCaptionSequences($entryIds, $captionFiles, $captionLangauges, $sequences, $mainEntry);

		$mediaSet = array('durations' => $durations, 'sequences' => $sequences);
		// build the json
		$json = self::jsonEncode($mediaSet);
		$renderer = new kRendererString($json, self::JSON_CONTENT_TYPE);

		$this->storeCache($renderer, $partnerId);

		$renderer->output();
		KExternalErrors::dieGracefully();
	}

	protected function verifySequenceEntries($sequenceEntries)
	{
		foreach ($sequenceEntries as $sequence)
		{
			/* @var entry $sequence */
			if (!in_array('sequence_entry',$sequence->getTagsArr()))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_SEQUENCE);
		}
		return true;

	}

	public function execute()
	{
		//entitlement should be disabled to serveFlavor action as we do not get ks on this action.
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		requestUtils::handleConditionalGet();

		$flavorId = $this->getRequestParameter("flavorId");
		$entryId = $this->getRequestParameter("entryId");
		$sequence = $this->getRequestParameter('sequence');
		$captionLanguages = $this->getRequestParameter('captions', '');
		$this->pathOnly = $this->getRequestParameter('pathOnly', false);
		self::$preferredStorageId = $this->getRequestParameter('preferredStorageId');
		self::$fallbackStorageId = $this->getRequestParameter('fallbackStorageId');

		$isAuthenticatedUri = kNetworkUtils::isAuthenticatedURI();
		if(kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']) || $isAuthenticatedUri)
		{
			self::$requestAuthorized = true;
		}

		if ($entryId)
		{
			$entry = entryPeer::retrieveByPKNoFilter($entryId);
			if (!$entry)
			{
				// rendering empty response in case entry was not replicated yet
				$this->renderEmptySimpleMapping();
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			}

			if ($entry->getStatus() == entryStatus::DELETED) {
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			}

			if( ($entry->getType() == entryType::LIVE_CHANNEL) && ($entry->getPlaylistId()) )
			{
				$this->servePlaylistAsLiveChannel($entry);
			}

			if ($entry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY) && $entry instanceof LiveEntry)
			{
				list($durations, $flavors, $startTime, $endTime, $dvrWindow) = kSimuliveUtils::getSimuliveEventDetails($entry);
				if ($flavors)
				{
					$sequences = self::buildSequencesArray($flavors);
					$mediaSet = $this->serveLiveMediaSet($durations, $sequences, $startTime, $startTime,
						null, null, true, true, $dvrWindow, $endTime);
					$this->sendJson($mediaSet, false, true, $entry);
				}
			}

			if ($entry->getType() == entryType::PLAYLIST && self::$requestAuthorized)
			{
				list($flavorParamId, $asset) = $this->getFlavorAssetAndParamIds($flavorId);
				myPartnerUtils::enforceDelivery($entry, $asset, self::$preferredStorageId);
				$this->servePlaylist($entry, $captionLanguages);
			}
			if ($sequence  && self::$requestAuthorized)
			{
				$sequenceArr = explode(',', $sequence);
				$sequenceEntries = entryPeer::retrieveByPKs($sequenceArr);
				if (count($sequenceEntries))
				{
					list($flavorParamId, $asset) = $this->getFlavorAssetAndParamIds($flavorId);
					myPartnerUtils::enforceDelivery($entry, $asset, self::$preferredStorageId);
					$this->verifySequenceEntries($sequenceEntries);
					$this->serveEntryWithSequence($entry, $sequenceEntries, $asset, $flavorParamId, $captionLanguages);
				}
			}
		}
		
		$shouldProxy = $this->getRequestParameter("forceproxy", false);
		$fileName = $this->getRequestParameter( "fileName" );
		$fileParam = $this->getRequestParameter( "file" );
		$fileParam = basename($fileParam);
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';
		
		$flavorAsset = assetPeer::retrieveByIdNoFilter($flavorId);
		if (is_null($flavorAsset)) {
			// rendering empty response in case flavor asset was not replicated yet
			$this->renderEmptySimpleMapping();
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		}

		if ($flavorAsset->getStatus() == asset::ASSET_STATUS_DELETED) {
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		}

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
			
		myPartnerUtils::enforceDelivery($entry, $flavorAsset, self::$preferredStorageId);
		
		$version = $this->getRequestParameter( "v" );
		if (!$version)
			$version = $flavorAsset->getVersion();
		
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $version);

		if ($this->pathOnly && self::$requestAuthorized)
		{
			list ($file_sync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($syncKey, $flavorAsset, self::getPreferredStorageProfileId(), self::getFallbackStorageProfileId());
			if ($file_sync && is_null(self::$preferredStorageId))
			{
				if ($fileParam && is_dir($path))
				{
					$path .= "/$fileParam";
				}
			}

			$renderer = $this->getSimpleMappingRenderer($path, $flavorAsset, $file_sync, $sourceType);
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
			if(!$isAuthenticatedUri)
			{
				KExternalErrors::dieError(KExternalErrors::INVALID_AUTH_HEADER);
			}
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
		
		
		if($fileParam && kFile::isDir($path)) {
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
						$cmd = kFfmpegUtils::getCopyCmd($path, $clipToSec, $tempClipPath);
						list($output, $return_value) = kFfmpegUtils::executeCmd($cmd, 0);
						KalturaLog::log("ffmpeg returned {$return_value}, output:".implode("\n", $output));
					}
					
					if (kFile::checkFileExists($tempClipPath))
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

	/**
	 * @param $entryIds
	 * @param $captionFiles
	 * @param $captionLangauges
	 * @param $sequences
	 * @return array
	 */
	protected function addCaptionSequences($entryIds, $captionFiles, $captionLangauges, &$sequences, $mainEntry)
	{
		$captionLangaugesArr = explode(',', $captionLangauges);
		foreach ($captionLangaugesArr as $captionLang)
		{
			$labelEntryId = null;
			$hasCaptions = false;
			$captionClips = array();
			foreach ($entryIds as $entryId)
			{
				if (isset($captionFiles[$entryId][$captionLang]))
				{
					$hasCaptions = true;
					$labelEntryId = $entryId;
					$captionClips[] = self::getAssetFieldsArray(self::TYPE_SOURCE, $captionFiles[$entryId][$captionLang][myPlaylistUtils::CAPTION_FILES_PATH], $captionFiles[$entryId][$captionLang][myPlaylistUtils::CAPTION_SOURCE_TYPE]);
				}
				else
				{
					$captionClips[] = self::getAssetFieldsArray(self::TYPE_SOURCE, self::PATH_EMPTY, kFileSyncUtils::SOURCE_TYPE_FILE);
				}
			}
			if ($hasCaptions)
			{
				$langString = $captionLang;
				if (isset(CaptionPlugin::$captionsFormatMap[$langString]))
					$langString = CaptionPlugin::$captionsFormatMap[$langString];
				$currSequence = array('clips' => $captionClips, 'language' => $langString);
				if (isset($captionFiles[$labelEntryId][$captionLang][myPlaylistUtils::CAPTION_FILES_LABEL]))
					$currSequence['label'] = $captionFiles[$labelEntryId][$captionLang][myPlaylistUtils::CAPTION_FILES_LABEL];
				$currSequence['id'] = $this->getServeUrlForFlavor($captionFiles[$labelEntryId][$captionLang][myPlaylistUtils::CAPTION_FILES_ID], $mainEntry->getId());
				$sequences[] = $currSequence;
			}
		}

		return true;
	}

	protected function getServeUrlForFlavor($flavorId, $entryId)
	{
		$url = $_SERVER['REQUEST_URI'];
		$prefix = substr($url, 0, strpos($url, 'serveFlavor/') + 12);
		$postfix = 'entryId/' . $entryId . "/flavorId/" . $flavorId . "/";
		$outUrl = $prefix . $postfix;
		return $outUrl;
	}

	/**
	 * @param $flavorId
	 * @return array
	 */
	protected function getFlavorAssetAndParamIds($flavorId)
	{
		$flavorParamId = null;
		$asset = null;
		if ($flavorId)
		{
			$asset = assetPeer::retrieveById($flavorId);
			if (is_null($asset))
			{
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
			}
			$flavorParamId = $asset->getFlavorParamsId();
		}
		return array($flavorParamId, $asset);
	}

	/**
	 * Flavor matching logic:
	 * 1. A flavor with more matching tags should be preferred over one with less matching tags (number of matching tags desc)
	 * 2. A flavor with less non-matching tags should be preferred (number of non-matching tags asc)
	 * 3. A flavor with a closer bitrate should be preferred
	 * @param $groupedFlavors
	 * @param $referenceFlavor
	 * @return mixed
	 */
	protected function getBestMatchFlavor($groupedFlavors, $referenceFlavor)
	{
		$flavor = reset($groupedFlavors);
		$matchingTags = count(array_intersect($flavor->getTagsArray(), $referenceFlavor->getTagsArray()));
		$nonMatchingTags = count(array_diff($flavor->getTagsArray(), $referenceFlavor->getTagsArray()));

		foreach ($groupedFlavors as $curFlavor)
		{
			$currMatchingTags = count(array_intersect($curFlavor->getTagsArray(), $referenceFlavor->getTagsArray()));
			$currNonMatchingTags = count(array_diff($curFlavor->getTagsArray(), $referenceFlavor->getTagsArray()));

			if ($currMatchingTags < $matchingTags)
				continue;

			if ($currMatchingTags > $matchingTags)
			{
				$flavor = $curFlavor;
				$matchingTags = $currMatchingTags;
				$nonMatchingTags = $currNonMatchingTags;
				continue;
			}

			if ($currNonMatchingTags > $nonMatchingTags)
				continue;

			if ($currNonMatchingTags < $nonMatchingTags)
			{
				$flavor = $curFlavor;
				$matchingTags = $currMatchingTags;
				$nonMatchingTags = $currNonMatchingTags;
				continue;
			}

			// case both flavors have the same matching and nonmatching tags - compare bitrates
			if (abs($curFlavor->getBitrate() - $referenceFlavor->getBitrate()) <
				abs($flavor->getBitrate() - $referenceFlavor->getBitrate())
			)
			{
				$flavor = $curFlavor;
				$matchingTags = $currMatchingTags;
				$nonMatchingTags = $currNonMatchingTags;
			}
		}
		return $flavor;
	}

	/**
	 * @param string $path
	 * @param flavorAsset $flavor
	 * @param $sourceType
	 * @return array
	 */
	public static function getClipData($path, $flavor, $sourceType)
	{
		$flavorId = $flavor->getId();
		$hasAudio = $flavor->getContainsAudio();
		if (is_null($hasAudio))
		{
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorId);
			$hasAudio = !$mediaInfo || $mediaInfo->isContainAudio();
		}
		$clipDesc = self::getAssetFieldsArray(self::TYPE_SOURCE, $path, $sourceType);
		if (!$hasAudio)
		{
			KalturaLog::debug("$flavorId Audio Bit rate is null or 0 (taken from mediaInfo)");
			$silent = array_merge(array(array('type' => 'silence')),array($clipDesc));
			$clipDesc = array('type' => 'mixFilter','sources' => $silent);
		}
		return $clipDesc ;
	}

	protected function getCurrentLiveChannelEntryIndex($cycleDurations, $currentCycleStartTime, $startTime)
	{
		$timeIterator = $currentCycleStartTime;
		$firstEntryIndex = -1;
		do{
			$firstEntryIndex++;
			$timeIterator += $cycleDurations[$firstEntryIndex];
		}while($timeIterator <= $startTime);

		return $firstEntryIndex;
	}

	protected function getCurrentLiveChannelEntryInfo($cycleDurations, $currentCycleStartTime, $cycleNumber, $firstEntryIndex, $segmentDuration,
	                                                  &$firstClipStartTime, &$initialClipIndex, &$initialSegmentIndex)
	{
		//Get supporting information
		$accumulateCycleSegmentsCount = array();
		$accumulateCycleDurations = array();

		$durationsSum = 0;
		$cycleSegmentsCount = 0;
		foreach ($cycleDurations as $duration)
		{
			$accumulateCycleDurations[] = $durationsSum;
			$durationsSum += $duration;

			$accumulateCycleSegmentsCount[] = $cycleSegmentsCount;
			$cycleSegmentsCount += ceil($duration / $segmentDuration);
		}

		// Set the values
		$firstClipStartTime = $currentCycleStartTime + $accumulateCycleDurations[$firstEntryIndex];

		$initialClipIndex = ($cycleNumber * count($cycleDurations)) + $firstEntryIndex + 1;
		$initialSegmentIndex = ($cycleNumber * $cycleSegmentsCount) + $accumulateCycleSegmentsCount[$firstEntryIndex] + 1;
	}

	protected function getCurrentLiveChannelEntries($cycleEntryIds, $cycleDurations, $firstClipStartTime, $firstEntryIndex, $currentTime,
	                                                &$entryIds, &$durations)
	{
		$entryIds = array();
		$durations = array();

		$cycleEntriesCount = count($cycleEntryIds);

		$entryIndex = $firstEntryIndex;
		$timeIterator = $firstClipStartTime;

		while($timeIterator < $currentTime)
		{
			$durations[] = $cycleDurations[$entryIndex];
			$entryIds[] = $cycleEntryIds[$entryIndex];

			$timeIterator += $cycleDurations[$entryIndex];

			$entryIndex++;
			if($entryIndex == $cycleEntriesCount)
			{
				$entryIndex = 0;
			}
		}
	}

	protected function getLiveParams()
	{
		$segmentDuration = null;
		$dvrWindowSize = null;

		$liveMap = kConf::getMap('live');
		foreach ($liveMap as $section => $params)
		{
			if(strstr($_SERVER['REQUEST_URI'],"/$section/"))
			{
				$segmentDuration = $params['segDuration'];
				$dvrWindowSize = $params['dvrWindowSize'];
				break;
			}
		}

		if(is_null($segmentDuration) || is_null($dvrWindowSize))
		{
			KExternalErrors::dieError(KExternalErrors::MISSING_LIVE_CONFIGURATION);
		}

		return array($segmentDuration, $dvrWindowSize);
	}

	/**
	 * @param array $flavors
	 * @return array
	 */
	public static function buildSequencesArray($flavors)
	{
		$sequences = array();

		foreach ($flavors as $flavor)
		{
			$syncKey = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			list ($file_sync, $path, $sourceType) = kFileSyncUtils::getFileSyncServeFlavorFields($syncKey, $flavor, self::getPreferredStorageProfileId(), self::getFallbackStorageProfileId());
			if(!$path)
			{
				KalturaLog::debug('missing path for flavor ' . $flavor->getId() . ' version ' . $flavor->getVersion());
				continue;
			}
			$sequences[] = array('clips' => array(self::getClipData($path, $flavor, $sourceType)));
		}
		return $sequences;
	}

	protected function servePlaylistAsLiveChannel(LiveChannel $entry)
	{
		// get cycle info
		$playlist = entryPeer::retrieveByPK($entry->getPlaylistId());

		list($cycleEntryIds, $cycleDurations, $referenceEntry, $captionFiles) = myPlaylistUtils::executeStitchedPlaylist($playlist);

		// Sanity
		$cycleDuration = array_sum($cycleDurations);
		if($cycleDuration == 0)
		{
			KExternalErrors::dieError(KExternalErrors::PLAYLIST_DURATION_IS_ZERO,
				"Entry [$entry->getId()] has a playlist with duration zero");
		}

		list($segmentDuration, $dvrWindowSize) = $this->getLiveParams();

		// Start time of the first run
		$playlistStartTime = $entry->getStartDate('U') * self::SECOND_IN_MILLISECONDS;

		// start window time and current time (which is the end time)
		$currentTime = time() * self::SECOND_IN_MILLISECONDS;
		$startTime = $currentTime - self::TIME_MARGIN - $dvrWindowSize;

		// Current cycle number
		$cycleNumber = floor(($startTime - $playlistStartTime) / $cycleDuration);

		// Set the time to the beginning of the current cycle (beginning of the 1st entry in the playlist)
		$currentCycleStartTime = $playlistStartTime + ($cycleNumber * $cycleDuration);

		// Find the entry that should be played now (when 'startTime' is in the middle of it).
		// Not necessarily the first entry in the playlist
		$firstEntryIndex = $this->getCurrentLiveChannelEntryIndex($cycleDurations, $currentCycleStartTime, $startTime);

		// Get Info about the first entry that should be played now
		$this->getCurrentLiveChannelEntryInfo($cycleDurations, $currentCycleStartTime, $cycleNumber, $firstEntryIndex, $segmentDuration,
			$firstClipStartTime, $initialClipIndex, $initialSegmentIndex);

		// Get Entries & Durations for the current window
		$this->getCurrentLiveChannelEntries($cycleEntryIds, $cycleDurations, $firstClipStartTime, $firstEntryIndex, $currentTime,
			$entryIds, $durations);

		//Make sure the referenceEntry is one of the entryIds
		if( ! in_array($referenceEntry->getId(), $entryIds) )
		{
			$referenceEntry = entryPeer::retrieveByPKNoFilter($entryIds[0]);
		}

		$this->serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $entry, null,
			$captionFiles, null, true,
			$playlistStartTime, $firstClipStartTime,
			$initialClipIndex, $initialSegmentIndex);
	}

	public static function getPreferredStorageProfileId()
	{
		return self::$preferredStorageId;
	}

	public static function getFallbackStorageProfileId()
	{
		return self::$fallbackStorageId;
	}

	public static function getAssetFieldsArray($type, $path, $sourceType)
	{
		return array(
			'type' => $type,
			'path' => $path,
			'sourceType' => $sourceType,
		);
	}
}
