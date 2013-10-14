<?php

/**
 * Live Stream service lets you manage live stream channels
 *
 * @service liveStream
 * @package api
 * @subpackage services
 */
class LiveStreamService extends KalturaEntryService
{
	const DEFAULT_BITRATE = 300;
	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;
	const ISLIVE_ACTION_CACHE_EXPIRY = 30;
	const HLS_LIVE_STREAM_CONTENT_TYPE = 'application/vnd.apple.mpegurl';
	
	
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'isLive') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	/**
	 * Adds new live stream entry.
	 * The entry will be queued for provision.
	 * 
	 * @action add
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata  
	 * @param KalturaSourceType $sourceType  Live stream source type
	 * @return KalturaLiveStreamAdminEntry The new live stream entry
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	function addAction(KalturaLiveStreamAdminEntry $liveStreamEntry, $sourceType = null)
	{
		//TODO: allow sourceType that belongs to LIVE entries only - same for mediaType
		if ($sourceType) {
			$liveStreamEntry->sourceType = $sourceType;
		}
		else {
			// default sourceType is AKAMAI_LIVE
			$liveStreamEntry->sourceType = kPluginableEnumsManager::coreToApi('EntrySourceType', $this->getPartner()->getDefaultLiveStreamEntrySourceType());
		}
		
		// if the given password is empty, generate a random 8-character string as the new password
		if ( ($liveStreamEntry->streamPassword == null) || (strlen(trim($liveStreamEntry->streamPassword)) <= 0) )
		{
			$tempPassword = sha1(md5(uniqid(rand(), true)));
			$liveStreamEntry->streamPassword = substr($tempPassword, rand(0,strlen($tempPassword)-8), 8);		
		}
		
		// if no bitrate given, add default
		if(is_null($liveStreamEntry->bitrates) || !$liveStreamEntry->bitrates->count)
		{
			$liveStreamBitrate = new KalturaLiveStreamBitrate();
			$liveStreamBitrate->bitrate = self::DEFAULT_BITRATE;
			$liveStreamBitrate->width = self::DEFAULT_WIDTH;
			$liveStreamBitrate->height = self::DEFAULT_HEIGHT;
			
			$liveStreamEntry->bitrates = new KalturaLiveStreamBitrateArray();
			$liveStreamEntry->bitrates[] = $liveStreamBitrate;
		}
		else 
		{
			$bitrates = new KalturaLiveStreamBitrateArray();
			foreach($liveStreamEntry->bitrates as $bitrate)
			{		
				if(is_null($bitrate->bitrate))	$bitrate->bitrate = self::DEFAULT_BITRATE;
				if(is_null($bitrate->width))	$bitrate->bitrate = self::DEFAULT_WIDTH;
				if(is_null($bitrate->height))	$bitrate->bitrate = self::DEFAULT_HEIGHT;
				$bitrates[] = $bitrate;
			}
			$liveStreamEntry->bitrates = $bitrates;
		}
		
		$dbEntry = $this->insertLiveStreamEntry($liveStreamEntry);
		
		myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD, $dbEntry, $this->getPartnerId(), null, null, null, $dbEntry->getId());

		$liveStreamEntry->fromObject($dbEntry);
		return $liveStreamEntry;
	}

	
	/**
	 * Get live stream entry by ID.
	 * 
	 * @action get
	 * @param string $entryId Live stream entry id
	 * @param int $version Desired version of the data
	 * @return KalturaLiveStreamEntry The requested live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function getAction($entryId, $version = -1)
	{
		return $this->getEntry($entryId, $version, KalturaEntryType::LIVE_STREAM);
	}

	
	/**
	 * Update live stream entry. Only the properties that were set will be updated.
	 * 
	 * @action update
	 * @param string $entryId Live stream entry id to update
	 * @param KalturaLiveStreamAdminEntry $liveStreamEntry Live stream entry metadata to update
	 * @return KalturaLiveStreamAdminEntry The updated live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @validateUser entry entryId edit
	 */
	function updateAction($entryId, KalturaLiveStreamAdminEntry $liveStreamEntry)
	{
		return $this->updateEntry($entryId, $liveStreamEntry, KalturaEntryType::LIVE_STREAM);
	}

	/**
	 * Delete a live stream entry.
	 *
	 * @action delete
	 * @param string $entryId Live stream entry id to delete
	 * 
 	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
 	 * @validateUser entry entryId edit
	 */
	function deleteAction($entryId)
	{
		$this->deleteEntry($entryId, KalturaEntryType::LIVE_STREAM);
	}
	
	/**
	 * List live stream entries by filter with paging support.
	 * 
	 * @action list
     * @param KalturaLiveStreamEntryFilter $filter live stream entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaLiveStreamListResponse Wrapper for array of live stream entries and total count
	 */
	function listAction(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
	    if (!$filter)
			$filter = new KalturaLiveStreamEntryFilter();
			
	    $filter->typeEqual = KalturaEntryType::LIVE_STREAM;
	    list($list, $totalCount) = parent::listEntriesByFilter($filter, $pager);
	    
	    $newList = KalturaLiveStreamEntryArray::fromEntryArray($list);
		$response = new KalturaLiveStreamListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	


	/**
	 * Update live stream entry thumbnail using a raw jpeg file
	 * 
	 * @action updateOfflineThumbnailJpeg
	 * @param string $entryId live stream entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaLiveStreamEntry The live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 */
	function updateOfflineThumbnailJpegAction($entryId, $fileData)
	{
		return parent::updateThumbnailJpegForEntry($entryId, $fileData, KalturaEntryType::LIVE_STREAM, entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB);
	}
	
	/**
	 * Update entry thumbnail using url
	 * 
	 * @action updateOfflineThumbnailFromUrl
	 * @param string $entryId live stream entry id
	 * @param string $url file url
	 * @return KalturaLiveStreamEntry The live stream entry
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::PERMISSION_DENIED_TO_UPDATE_ENTRY
	 */
	function updateOfflineThumbnailFromUrlAction($entryId, $url)
	{
		return parent::updateThumbnailForEntryFromUrl($entryId, $url, KalturaEntryType::LIVE_STREAM, entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB);
	}
	
	private function insertLiveStreamEntry(KalturaLiveStreamAdminEntry $liveStreamEntry)
	{
		// create a default name if none was given
		if (!$liveStreamEntry->name)
			$liveStreamEntry->name = $this->getPartnerId().'_'.time();
		
		// first copy all the properties to the db entry, then we'll check for security stuff
		$dbEntry = $liveStreamEntry->toInsertableObject(new entry());

		$this->checkAndSetValidUserInsert($liveStreamEntry, $dbEntry);
		$this->checkAdminOnlyInsertProperties($liveStreamEntry);
		$this->validateAccessControlId($liveStreamEntry);
		$this->validateEntryScheduleDates($liveStreamEntry, $dbEntry);
		/* @var $dbEntry entry */
		$dbEntry->setPartnerId($this->getPartnerId());
		$dbEntry->setSubpId($this->getPartnerId() * 100);
		$dbEntry->setKuserId($this->getKuser()->getId());
		$dbEntry->setCreatorKuserId($this->getKuser()->getId());
		$dbEntry->setStatus(entryStatus::IMPORT);
		
		$te = new TrackEntry();
		$te->setEntryId( $dbEntry->getId() );
		$te->setTrackEventTypeId( TrackEntry::TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY );
		$te->setDescription(  __METHOD__ . ":" . __LINE__ . "::ENTRY_MEDIA_SOURCE_AKAMAI_LIVE" );
		TrackEntry::addTrackEntry( $te );
		
		$dbEntry->save();
		//If a jobData can be created for entry sourceType, add provision job. Otherwise, just save the entry.
		$jobData = kProvisionJobData::getInstance($dbEntry->getSource());
		if ($jobData)
		{
			/* @var $data kProvisionJobData */
			$jobData->populateFromPartner($dbEntry->getPartner());
			$jobData->populateFromEntry($dbEntry);
			kJobsManager::addProvisionProvideJob(null, $dbEntry, $jobData);
		}
		else
		{
			$dbEntry->setStatus(entryStatus::READY);
			$dbEntry->save();
		}
 			
		return $dbEntry;
	}	
	
	/**
	 * New action delivering the status of a live stream (on-air/offline) if it is possible
	 * @action isLive
	 * @param string $id ID of the live stream
	 * @param KalturaPlaybackProtocol $protocol protocol of the stream to test.
	 * @throws KalturaErrors::LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @return bool
	 */
	public function isLiveAction ($id, $protocol)
	{
		KalturaResponseCacher::setExpiry(self::ISLIVE_ACTION_CACHE_EXPIRY);
		kApiCache::disableConditionalCache();
		if (!kCurrentContext::$ks)
		{
			$liveStreamEntry = kCurrentContext::initPartnerByEntryId($id);
			if (!$liveStreamEntry || $liveStreamEntry->getStatus() == entryStatus::DELETED)
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);

			// enforce entitlement
			$this->setPartnerFilters(kCurrentContext::getCurrentPartnerId());
			kEntitlementUtils::initEntitlementEnforcement(null, false);
		}
		else
		{
			$liveStreamEntry = entryPeer::retrieveByPK($id);
		}
		if (!$liveStreamEntry || ($liveStreamEntry->getType() != entryType::LIVE_STREAM))
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $id);
		
		switch ($protocol)
		{
			case KalturaPlaybackProtocol::HLS:
				KalturaLog::info('Determining status of live stream URL [' .$liveStreamEntry->getHlsStreamUrl(). ']');
				$url = $liveStreamEntry->getHlsStreamUrl();
				$config = kLiveStreamConfiguration::getSingleItemByPropertyValue($liveStreamEntry, 'protocol', $protocol);
				if ($config)
					$url = $config->getUrl();
				$url = $this->getTokenizedUrl($id, $url, $protocol);
				return $this->hlsUrlExistsRecursive($url);
				break;
				
			case KalturaPlaybackProtocol::HDS:
			case KalturaPlaybackProtocol::AKAMAI_HDS:
				$config = kLiveStreamConfiguration::getSingleItemByPropertyValue($liveStreamEntry, "protocol", $protocol);
				if ($config)
				{
					$url = $this->getTokenizedUrl($id,$config->getUrl(),$protocol);
					if ($protocol == KalturaPlaybackProtocol::AKAMAI_HDS || in_array($liveStreamEntry->getSource(), array(EntrySourceType::AKAMAI_LIVE,EntrySourceType::AKAMAI_UNIVERSAL_LIVE))){
						$url .= '?hdcore='.kConf::get('hd_core_version');
					}
					KalturaLog::info('Determining status of live stream URL [' .$url . ']');
					return $this->hdsUrlExists($url);
				}
				break;
		}
		
		throw new KalturaAPIException(KalturaErrors::LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED, $protocol);
	}
	
	/**
	 * 
	 * get tokenized url if exists
	 * @param string $entryId
	 * @param string $url
	 * @param string $protocol
	 */
	private function getTokenizedUrl($entryId, $url, $protocol){
		$urlPath = parse_url($url, PHP_URL_PATH);
		if (!$urlPath || substr($url, -strlen($urlPath)) != $urlPath)
			return $url;
		$urlPrefix = substr($url, 0, -strlen($urlPath));
		$cdnHost = parse_url($url, PHP_URL_HOST);		
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $entryId);
		if ($urlManager){
			$urlManager->setProtocol($protocol);
			$tokenizer = $urlManager->getTokenizer();
			if ($tokenizer)
				return $urlPrefix.$tokenizer->tokenizeSingleUrl($urlPath);
		}
		return $url;
	}
	
	
	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return string
	 */
	private function urlExists ($url, array $contentTypeToReturn)
	{
		if (is_null($url)) 
			return false;  
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
			return false;  
		}
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  
	    $data = curl_exec($ch);  
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	    curl_close($ch);  
	    if($data && $httpcode>=200 && $httpcode<300)
	    {
	        return in_array($contentType, $contentTypeToReturn) ? $data : true;
	    }  
	    else 
	        return false;  
	}	
	
	/**
	 * Recursive function which returns true/false depending whether the given URL returns a valid video eventually
	 * @param string $url
	 * @return bool
	 */
	private function hlsUrlExistsRecursive ($url)
	{
		$data = $this->urlExists($url, kConf::get("hls_live_stream_content_type"));
		if(!$data)
		{
			KalturaLog::Info("URL [$url] returned no valid data. Exiting.");
			return $data;
		}

		$lines = explode("#EXT-X-STREAM-INF:", trim($data));

		foreach ($lines as $line)
		{
			if(!preg_match('/.+\.m3u8/', array_shift($lines), $matches))
				continue;
			$streamUrl = $matches[0];
			$streamUrl = $this->checkIfValidUrl($streamUrl, $url);
			
			$data = $this->urlExists($streamUrl, kConf::get("hls_live_stream_content_type"));
			if (!$data)
				continue;
				
			$segments = explode("#EXTINF:", $data);
			if(!preg_match('/.+\.ts.*/', array_pop($segments), $matches))
				continue;
			
			$tsUrl = $matches[0];
			$tsUrl = $this->checkIfValidUrl($tsUrl, $url);
			if ($this->urlExists($tsUrl ,kConf::get("hls_live_stream_content_type")))
				return true;
		}
			
		return false;
	}
	
	/**
	 * Function check if URL provided is a valid one if not returns fixed url with the parent url relative path
	 * @param string $urlToCheck
	 * @param string $parentURL
	 * @return fixed url path 
	 */
	private function checkIfValidUrl($urlToCheck, $parentURL)
	{
		if(!filter_var($urlToCheck, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
		{
			$urlToCheck = dirname($parentURL) . DIRECTORY_SEPARATOR . $urlToCheck;
		}
		
		return $urlToCheck;
	}
	
	/**
	 * Function which returns true/false depending whether the given URL returns a live video
	 * @param string $url
	 * @return true
	 */
	private function hdsUrlExists ($url) 
	{
		$data = $this->urlExists($url, array('video/f4m'));
		if (is_bool($data))
			return $data;
		
		$element = new KDOMDocument();
		$element->loadXML($data);
		$streamType = $element->getElementsByTagName('streamType')->item(0);
		if ($streamType->nodeValue == 'live')
			return true;
		
		return false;
	}
}