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
	
	protected function getSimpleMappingRenderer($path, asset $asset)
	{
		$source = array(
			'type' => 'source',
			'path' => $path,
		);

		if ($asset->getEncryptionKey())
		{
			$source['encryptionKey'] = $asset->getEncryptionKey();
		}
		
		$sequence = array(
			'clips' => array($source)
		);

		if (method_exists($asset, 'getLanguage') && $asset->getLanguage())
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

		if (method_exists($asset, 'getLabel') && $asset->getLabel())
		{
			$sequence['label'] = $asset->getLabel();
		}
		
		$result = array(
			'sequences' => array($sequence)
		);

		$json = str_replace('\/', '/', json_encode($result));

		return new kRendererString(
				$json,
				self::JSON_CONTENT_TYPE);
	}
	
	protected function serveLivePlaylist($durations, $sequences)
	{
		$referenceTime = 1451624400000;	// 2016-01-01
		$segmentDuration = 10000;
		$segmentCount = 10;
		$timeMargin = 10000;			// a safety margin to compensate for clock differences
					
		// find the duration of each cycle
		$cycleDuration = array_sum($durations);
		$dvrWindowSize = $segmentDuration * $segmentCount;
		
		// if the cycle is too small to cover the DVR window, duplicate it
		while ($cycleDuration <= $dvrWindowSize + $timeMargin)
		{
			foreach ($sequences as &$sequence)
			{
				$sequence['clips'] = array_merge($sequence['clips'], $sequence['clips']);
			}
			$durations = array_merge($durations, $durations);
			$cycleDuration *= 2;
		}
			
		$currentTime = time() * 1000 - $timeMargin - $dvrWindowSize;
			
		$mediaSet['playlistType'] = 'live';
		$mediaSet['segmentBaseTime'] = $referenceTime;
		$mediaSet['firstClipTime'] = floor($currentTime / $cycleDuration) * $cycleDuration;
		$mediaSet['discontinuity'] = false;
		
		// duplicate the clips, this is required so that we won't run out of segments
		// close to the end of a cycle
		foreach ($sequences as &$sequence)
		{
			$sequence['clips'] = array_merge($sequence['clips'], $sequence['clips']);
		}
		$durations = array_merge($durations, $durations);
		
		$mediaSet['durations'] = $durations;
		$mediaSet['sequences'] = $sequences;
		
		return $mediaSet;
	}
	
	protected function servePlaylist($entry)
	{
		// allow only manual playlist
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_TEXT)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}

		$version = $this->getRequestParameter("v");

		// execute the playlist
		if ($version)
		{
			$entry->setDesiredVersion($version);
		}
		
		list($entryIds, $durations, $referenceEntry, $captionFiles) =
			myPlaylistUtils::executeStitchedPlaylist($entry);
		$this->serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $entry, null);
	}

	protected function serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $origEntry, $flavorParamIds, $captionFiles, $captionLanguages)
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
		$isLive = $this->getRequestParameter("live");

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
		foreach ($flavorParamIds as $flavorParamsId)
		{
			$referenceFlavor = $groupedFlavors[$referenceEntry->getId()][$flavorParamsId];

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
					// don't have a flavor for this entry in the desired flavor params,
					// choose the one with the closest bitrate
					$flavor = reset($groupedFlavors[$entryId]);
					foreach ($groupedFlavors[$entryId] as $curFlavor)
					{
						// first priority - matching tags
						if ($flavor->getTags() == $referenceFlavor->getTags())
						{
							if ($curFlavor->getTags() != $referenceFlavor->getTags())
							{
								continue;
							}
						}
						else
						{
							if ($curFlavor->getTags() == $referenceFlavor->getTags())
							{
								$flavor = $curFlavor;
								continue;
							}
						}

						// second priority - bitrate
						if (abs($curFlavor->getBitrate() - $referenceFlavor->getBitrate()) <
							abs($flavor->getBitrate() - $referenceFlavor->getBitrate()))
						{
							$flavor = $curFlavor;
						}
					}
				}

				// get the file path of the flavor
				$syncKey = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey , false, false);
				if ($fileSync)
				{
					$resolvedFileSync = kFileSyncUtils::resolve($fileSync);
					$path = $resolvedFileSync->getFullPath();
				}
				else
				{
					error_log('missing file sync for flavor ' . $flavor->getId() . ' version ' . $flavor->getVersion());
					$path = '';
					$storeCache = false;
				}

				$clips[] = array('type' => 'source', 'path' => $path);
			}
			$sequences[] = array('clips' => $clips);
		}

		$this->addCaptionSequences($entryIds, $captionFiles, $captionLanguages, $sequences);

		// build the media set
		if ($isLive)
		{
			$mediaSet = $this->serveLivePlaylist($durations, $sequences);
		}
		else
		{
			$mediaSet = array('durations' => $durations, 'sequences' => $sequences);
		}

		// build the json
		$json = json_encode($mediaSet);
		$renderer = new kRendererString($json, self::JSON_CONTENT_TYPE);
		if ($storeCache && !$isLive)
		{
			$this->storeCache($renderer, $origEntry->getPartnerId());
		}

		$renderer->output();
		KExternalErrors::dieGracefully();
	}


	protected function serveEntryWithSequence($entry, $sequenceEntries, $flavorId, $captionLanguages)
	{
		list($assets, $flavorParamsIdsArr) = $this->getFlavorParamIds($flavorId);
		$allEntries = $sequenceEntries;
		$allEntries[] = $entry;
		if (empty($captionLanguages) && $assets && (count($assets) == 1) && $assets[0]->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			$captionLanguages = $assets[0]->getLanguage();
		list($entryIds, $durations, $referenceEntry, $captionFiles ) =
			myPlaylistUtils::getPlaylistDataFromEntries($allEntries, $flavorParamsIdsArr, $captionLanguages);

		if ($assets && (count($assets) == 1) && $assets[0]->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
		{
			$this->serveCaptionsWithSequence($entryIds, $captionFiles, $durations, $captionLanguages, $entry->getPartnerId());
		}


		$this->serveEntriesAsPlaylist($entryIds, $durations, $referenceEntry, $entry, $flavorParamsIdsArr, $captionFiles, $captionLanguages);
	}

	protected function serveCaptionsWithSequence($entryIds, $captionFiles, $durations, $captionLangauges, $partnerId)
	{
		$sequences = array();

		$this->addCaptionSequences($entryIds, $captionFiles, $captionLangauges, $sequences);

		$mediaSet = array('durations' => $durations, 'sequences' => $sequences);
		// build the json
		$json = json_encode($mediaSet);
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

		if ($entryId)
		{
			$entry = entryPeer::retrieveByPK($entryId);
			if (!$entry)
			{
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			}
			$isInternalIp = kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']);
			if ($entry->getType() == entryType::PLAYLIST && $isInternalIp)
			{
				$this->servePlaylist($entry);
			}
			if ($sequence  && $isInternalIp)
			{
				$sequenceArr = explode(',', $sequence);
				$sequenceEntries = entryPeer::retrieveByPKs($sequenceArr);
				if (count($sequenceEntries))
				{
					$this->verifySequenceEntries($sequenceEntries);
					$this->serveEntryWithSequence($entry, $sequenceEntries, $flavorId, $captionLanguages);
				}
			}
		}
		
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
			$path = '';
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
		
			$renderer = $this->getSimpleMappingRenderer($path, $flavorAsset);
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

	/**
	 * @param $entryIds
	 * @param $captionFiles
	 * @param $captionLangauges
	 * @param $sequences
	 * @return array
	 */
	protected function addCaptionSequences($entryIds, $captionFiles, $captionLangauges, &$sequences)
	{
		$captionLangaugesArr = explode(',', $captionLangauges);
		foreach ($captionLangaugesArr as $captionLang)
		{
			$hasCaptions = false;
			$captionClips = array();
			foreach ($entryIds as $entryId)
			{
				if (isset($captionFiles[$entryId][$captionLang]))
				{
					$hasCaptions = true;
					$captionClips[] = array('type' => 'source', 'path' => $captionFiles[$entryId][$captionLang][1]);
				}
				else
				{
					$captionClips[] = array('type' => 'source', 'path' => 'empty');
				}
			}
			if ($hasCaptions)
			{
				$langString = $captionLang;
				if (isset(CaptionPlugin::$captionsFormatMap[$langString]))
					$langString = CaptionPlugin::$captionsFormatMap[$langString];
				$currSequence = array('clips' => $captionClips, 'language' => $langString);
				if (!is_null($captionFiles[$entryId][$captionLang][0]))
					$currSequence['label'] = $captionFiles[$entryId][$captionLang][0];
				$sequences[] = $currSequence;
			}
		}

		return true;
	}

	/**
	 * @param $flavorId
	 * @return array
	 */
	protected function getFlavorParamIds($flavorId)
	{
		$flavorParamsIdsArr = array();
		$assets = null;
		if ($flavorId)
		{
			$flavorIdsArr = explode(',', $flavorId);
			$actualFlavorIds = array();
			if (count($flavorIdsArr) > 1)
			{
				for ($i = 1; $i < count($flavorIdsArr); $i++)
				{
					$actualFlavorIds[] = $flavorIdsArr[0] . $flavorIdsArr[$i];
				}
				$assets = assetPeer::retrieveByIds($actualFlavorIds);
			}
			else
			{
				$assets = array(assetPeer::retrieveById($flavorId));
			}
			if (count($assets) <= 0)
			{
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
			}
			foreach ($assets as $asset)
			{
				$flavorParamsIdsArr[] = $asset->getFlavorParamsId();
			}
		}
		return array($assets, $flavorParamsIdsArr);
	}
}
