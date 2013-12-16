<?php
/**
 * @package Core
 * @subpackage storage
 */
class kUrlManager
{
	/**
	 * @var array
	 */
	protected $params = null;
	
	/**
	 * @var string
	 */
	protected $extention = null;
	
	/**
	 * @var string
	 */
	protected $containerFormat = null;
	
	/**
	 * @var int
	 */
	protected $seekFromTime = null;
	
	/**
	 * @var int
	 */
	protected $clipTo = null;
	
	/**
	 * @var string
	 */
	protected $protocol = PlaybackProtocol::HTTP;
	
	/**
	 * @var string
	 */
	protected $domain = null;
	
	/**
	 * @var int
	 */
	protected $storageProfileId = null;
	
	/**
	 * @var string
	 */
	protected $entryId = null;
	
	/**
	 * @param string $cdnHost
	 * @param string $entryId
	 * @return kUrlManager
	 */
	public static function getUrlManagerByCdn($cdnHost, $entryId)
	{
		$class = 'kUrlManager';
		
		$cdnHost = preg_replace('/https?:\/\//', '', $cdnHost);
		$params = null;
	
		$urlManagers = kConf::getMap('url_managers');
		if(isset($urlManagers[$cdnHost]))
		{
			$class = $urlManagers[$cdnHost]["class"];
			$params = isset($urlManagers[$cdnHost]["params"]) ? $urlManagers[$cdnHost]["params"] : array();
			$entry = entryPeer::retrieveByPK($entryId);
			$urlManagersMap = kConf::getMap('url_managers');
			if ($entry && isset($urlManagersMap["override"]))
			{
				$overrides = $urlManagersMap["override"];
				$partnerId = $entry->getPartnerId();
				if (array_key_exists($partnerId, $overrides))
				{
					$overrides = $overrides[$partnerId];
					foreach($overrides as $override)
					{
						if ($override['domain'] == $cdnHost)
							$params = array_merge($params, $override["params"]);
					}
				}
			}
		}
			
		KalturaLog::log("Uses url manager [$class]");
		return new $class(null, $params, $entryId);
	}
	
	/**
	 * @param int $storageProfileId
	 * @param string $entryId
	 * @return kUrlManager
	 */
	public static function getUrlManagerByStorageProfile($storageProfileId, $entryId)
	{
		$class = 'kUrlManager';
		$params = null;
		
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		
		KalturaLog::debug("Url manager for storageProfile <$storageProfileId> is: ".$storageProfile->getUrlManagerClass());
		
		if($storageProfile && $storageProfile->getUrlManagerClass() && class_exists($storageProfile->getUrlManagerClass()))
		{
			$class = $storageProfile->getUrlManagerClass();
		    $params = $storageProfile->getUrlManagerParams();
		}
			
		KalturaLog::log("Uses url manager [$class]");
		return new $class($storageProfileId, $params, $entryId);
	}
	
	public function __construct($storageProfileId = null, $params = null, $entryId = null)
	{
		$this->storageProfileId = $storageProfileId;
		$this->params = $params ? $params : array();
		$this->entryId = $entryId;
	}
	
	/**
	 * @param string $ext
	 */
	public function setFileExtension($ext)
	{
		$this->extention = $ext;
	}
	
	/**
	 * @param string $containerFormat
	 */
	public function setContainerFormat($containerFormat)
	{
		$this->containerFormat = $containerFormat;
	}
	
	/**
	 * @return int
	 */
	public function getSeekFromBytes($path)
	{
		if($this->seekFromTime <= 0)
			return null;
			
		$flvWrapper = new myFlvHandler($path);
		if(!$flvWrapper->isFlv())
			return null;
			
		$audioOnly = false;
		if($flvWrapper->getFirstVideoTimestamp() < 0 )
			$audioOnly = true;
		
		list ( $bytes , $duration ,$firstTagByte , $toByte ) = $flvWrapper->clip(0, -1, $audioOnly);
		list ( $bytes , $duration ,$fromByte , $toByte, $seekFromTimestamp ) = $flvWrapper->clip($this->seekFromTime, -1, $audioOnly);
		$seekFromBytes = myFlvHandler::FLV_HEADER_SIZE + $flvWrapper->getMetadataSize($audioOnly) + $fromByte - $firstTagByte;
		
		return $seekFromBytes;
	}
	
	/**
	 * @param int $seek miliseconds
	 */
	public function setSeekFromTime($seek)
	{
		$this->seekFromTime = $seek;
	}
	
	/**
	 * @param int $clipTo seconds
	 */
	public function setClipTo($clipTo)
	{
		$this->clipTo = $clipTo;
	}
	
	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}
	
	/**
	 * @param string $domain
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
	}
	
	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}

	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @param FileSync $fileSync
	 * @param bool $tokenizeUrl
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync, $tokenizeUrl = true)
	{
		$url = $this->doGetFileSyncUrl($fileSync);
		if ($tokenizeUrl)
		{
			$tokenizer = $this->getTokenizer();
			if ($tokenizer)
			{
				$url = $tokenizer->tokenizeSingleUrl($url);
				kApiCache::disableCache();
			}
		}
		return $url;
	}
	
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		if($fileSync->getObjectSubType() == entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return $fileSync->getSmoothStreamUrl();
		
		$url = $fileSync->getFilePath();
		$url = str_replace('\\', '/', $url);
	
		if($this->protocol == PlaybackProtocol::RTMP)
		{
		    $storageProfile = StorageProfilePeer::retrieveByPK($this->storageProfileId);
		    if ($storageProfile->getRTMPPrefix())
			{
			    if (strpos($url, '/') !== 0)
			    {
			        $url = '/'.$url;
			    }
			    $url = $storageProfile->getRTMPPrefix(). $url;
			}
			if (($this->extention && strtolower($this->extention) != 'flv' ||
				$this->containerFormat && strtolower($this->containerFormat) != 'flash video'))
				$url = "mp4:".ltrim($url,'/');
				
			// when serving files directly via RTMP fms doesnt expect to get the file extension
			$url = str_replace('.mp4', '', str_replace('.flv','',$url));
		}
				
		return $url;
	}
	
	/**
	 * @param thumbAsset $thumbAsset
	 * @return string
	 */
	protected function doGetThumbnailAssetUrl(thumbAsset $thumbAsset)
	{
		$thumbAssetId = $thumbAsset->getId();
		$partnerId = $thumbAsset->getPartnerId();
		$url = "/api_v3/service/thumbAsset/action/serve/partnerId/$partnerId/thumbAssetId/$thumbAssetId";
		
		return $url;
	}

	/**
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if (isset($this->params['enforce_rtmpe']) && $this->params['enforce_rtmpe'])
		{
			$baseUrl = preg_replace('/^rtmp:\/\//', 'rtmpe://', $baseUrl);
			$baseUrl = preg_replace('/^rtmpt:\/\//', 'rtmpte://', $baseUrl);
		}

		if (isset($this->params['extra_params']) && $this->params['extra_params'] && !$flavorsUrls)
		{
			$parsedUrl = parse_url($baseUrl);
			if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
				$baseUrl .= '&';
			else
				$baseUrl .= '?';
			$baseUrl .= $this->params['extra_params'];
		}
	}

	/**
	 * @param asset $asset
	 * @param bool $tokenizeUrl
	 * @return string
	 */
	public function getAssetUrl(asset $asset, $tokenizeUrl = true)
	{
		$url = null;
		
		if($asset instanceof thumbAsset)
			$url = $this->doGetThumbnailAssetUrl($asset);
		
		if($asset instanceof flavorAsset)
		{
			$url = $this->doGetFlavorAssetUrl($asset);
			if ($tokenizeUrl)
			{
				$tokenizer = $this->getTokenizer();
				if ($tokenizer)
				{
					$url = $tokenizer->tokenizeSingleUrl($url);
					kApiCache::disableCache();
				}
			}
		}
			
		return $url;
	}

	/**
	 * @param flavorAsset $asset
	 * @param string $clientTag
	 * @return string
	 */
	public function getPlayManifestUrl(flavorAsset $asset, $clientTag)
	{
		$entryId = $asset->getEntryId();
		$partnerId = $asset->getPartnerId();
		$subpId = $asset->getentry()->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$flavorAssetId = $asset->getId();
		$cdnHost = parse_url($this->domain, PHP_URL_HOST);
		
		$url = "$partnerPath/playManifest/entryId/$entryId/flavorId/$flavorAssetId/protocol/{$this->protocol}/format/url/cdnHost/$cdnHost/clientTag/$clientTag";
		if($this->storageProfileId)
			$url .= "/storageId/$this->storageProfileId";
		
		return $url;
	}

	protected function getFlavorVersionString(flavorAsset $flavorAsset)
	{
		$entry = $flavorAsset->getentry();
		$partner = $entry->getPartner();

		$flavorAssetVersion = $flavorAsset->getVersion();
		$partnerFlavorVersion = $partner->getCacheFlavorVersion();
		$entryFlavorVersion = $entry->getCacheFlavorVersion();

		return (!$flavorAssetVersion ? '' : "/v/$flavorAssetVersion").
			($partnerFlavorVersion ? "/pv/$partnerFlavorVersion" : '') .
			($entryFlavorVersion ? "/ev/$entryFlavorVersion" : '');
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $flavorAsset->getentry()->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$flavorAssetId = $flavorAsset->getId();
		
		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());
	
		$versionString = $this->getFlavorVersionString($flavorAsset);
		$url = "$partnerPath/serveFlavor/entryId/".$flavorAsset->getEntryId()."{$versionString}/flavorId/$flavorAssetId";
		if($this->seekFromTime > 0)
			$url .= "/seekFrom/$this->seekFromTime";
			
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";
			
		switch($this->protocol){
		case PlaybackProtocol::RTMP:
			$url .= '/forceproxy/true';
			if ($this->extention && strtolower($this->extention) != 'flv' ||
                        	$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
			{
				$url = "mp4:".ltrim($url,'/')."/name/a.mp4";
			}
			else
			{
				$url .= "/name/a.flv";
			}
			break;
		case PlaybackProtocol::APPLE_HTTP:
			$url .= "/file/playlist.m3u8";
			break;
		case PlaybackProtocol::HTTP:
			if($this->extention)
				$url .= "/name/a.".$this->extention;
			break;
		}
		
		$url = str_replace('\\', '/', $url);
		return $url;
	}

	/**
	 * check whether this url manager sent the current request.
	 * if so, return a string describing the usage. e.g. cdn.acme.com+token for
	 * using cdn.acme.com with secure token delivery. This string can be matched to the
	 * partner settings in order to enforce a specific delivery method.
	 * @return string
	 */
	public function identifyRequest()
	{
		return false;
	}
	
	/**
	 * find the url manager which sent the current request
	 * @return string
	 */
	public static function getUrlManagerIdentifyRequest()
	{
		$urlManagers = kConf::getMap('url_managers');
		foreach($urlManagers as $cdnHost => $data)
		{
			if (!isset($data["class"]))
				continue;

			$class = $data["class"];
			$params = @$data["params"];
			$manager = new $class(null, $params);

			$result = $manager->identifyRequest();
			if ($result !== false)
				return $result;
		}

		// if the host wasnt specificed in the url_managers.ini use the http HTTP_X_FORWARDED_HOST or HOST header
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
			return $_SERVER['HTTP_X_FORWARDED_HOST'];

		if (isset($_SERVER['HTTP_HOST']))
			return $_SERVER['HTTP_HOST'];

		return false;
	}
	
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		return null;
	}
	
	/**
	 * Checking if the url is live or not. 
	 * @param string $url the url to check
	 * @throws Exception
	 */
	public function isLive($url){
		$url = $this->getTokenizedUrl($url);
		switch ($this->protocol){
			case PlaybackProtocol::HDS:
			case PlaybackProtocol::AKAMAI_HDS:
				return $this->isHdsLive($url);
				break;
			case PlaybackProtocol::HLS:
			case PlaybackProtocol::APPLE_HTTP:
				return $this->isHlsLive($url);
				break;
		}
		throw new Exception('Status cannot be determined for live stream protocol '.$this->protocol);
	}
	
	/**
	 * get tokenized url if exists
	 * @param string $url
	 */
	private function getTokenizedUrl($url){
		$urlPath = parse_url($url, PHP_URL_PATH);
		if (!$urlPath || substr($url, -strlen($urlPath)) != $urlPath)
			return $url;
		$urlPrefix = substr($url, 0, -strlen($urlPath));
		$tokenizer = $this->getTokenizer();
		if ($tokenizer)
			return $urlPrefix.$tokenizer->tokenizeSingleUrl($urlPath);
		return $url;
	}
	
	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return string
	 */
	protected function urlExists ($url, array $contentTypeToReturn, $range = null)
	{
		if (is_null($url)) 
			return false;  
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
			return false;  
		}
		KalturaLog::log("Checking URL [$url] with range [$range]");
		$ch = curl_init($url);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (!is_null($range))
		{
			curl_setopt($ch, CURLOPT_RANGE, $range);
		}
		$data = curl_exec($ch);  
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch); 

		$contentTypeToCheck = strstr($contentType, ";", true);
		if(!$contentTypeToCheck)
			$contentTypeToCheck = $contentType;
		if($data && $httpcode>=200 && $httpcode<300)
		{
			return in_array(trim($contentTypeToCheck), $contentTypeToReturn) ? $data : true;
		}  
		else 
			return false;  
	}	
	
	/**
	 * Recursive function which returns true/false depending whether the given URL returns a valid video eventually
	 * @param string $url
	 * @return bool
	 */
	public function isHlsLive ($url)
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
			$explodedLine = explode("\n", $line);
			// find a line that does not start with #
			array_shift($explodedLine);	// drop the line of the EXT-X-STREAM-INF
			$streamUrl = null;
			foreach ($explodedLine as $curLine)
			{
				$curLine = trim($curLine);
				if (!$curLine || $curLine[0] == '#')
					continue;
				$streamUrl = $curLine;
				break;
			}
			if (!$streamUrl || strpos($streamUrl, '.m3u8') === false)
				continue;
			$streamUrl = $this->checkIfValidUrl($streamUrl, $url);
			
			$data = $this->urlExists($streamUrl, kConf::get("hls_live_stream_content_type"));
			if (!$data)
				continue;
				
			$segments = explode("#EXTINF:", $data);
			if(!preg_match('/.+\.ts.*/', array_pop($segments), $matches))
				continue;
			
			$tsUrl = $matches[0];
			$tsUrl = $this->checkIfValidUrl($tsUrl, $url);
			if ($this->urlExists($tsUrl ,kConf::get("hls_live_stream_content_type"),'0-0') !== false)
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
	protected function checkIfValidUrl($urlToCheck, $parentURL)
	{
		$urlToCheck = trim($urlToCheck);
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
	public function isHdsLive ($url) 
	{
		$liveStreamEntry = entryPeer::retrieveByPK($this->entryId); 
		if ($this->protocol == PlaybackProtocol::AKAMAI_HDS || in_array($liveStreamEntry->getSource(), array(EntrySourceType::AKAMAI_LIVE,EntrySourceType::AKAMAI_UNIVERSAL_LIVE))){
			$parsedUrl = parse_url($url);
			if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
				$url .= '&hdcore='.kConf::get('hd_core_version');
			else
				$url .= '?hdcore='.kConf::get('hd_core_version');
		}
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
