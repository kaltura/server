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
			$params = @$urlManagers[$cdnHost]["params"];
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
				$url = "mp4:$url";
				
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

	protected function getFlavorVersionString(flavorAsset $flavorAsset)
	{
		$entry = $flavorAsset->getentry();
		$partner = $entry->getPartner();

		$flavorAssetVersion = $flavorAsset->getVersion();
		$partnerFlavorVersion = $partner->getCacheFlavorVersion();
		$entryFlavorVersion = $entry->getCacheFlavorVersion();

		return (!$flavorAssetVersion || $flavorAssetVersion == 1 ? '' : "/v/$flavorAssetVersion").
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
				$url = "mp4:$url/name/a.mp4";
			}
			else
			{
				$url .= "/name/a.flv";
			}
			break;
		case PlaybackProtocol::APPLE_HTTP:
			$url .= "/file/playlist.m3u8";
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
			$class = $data["class"];
			$params = @$data["params"];
			$manager = new $class(null, $params);

			$result = $manager->identifyRequest();
			if ($result !== false)
				return $result;
		}

		return false;
	}
	
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		return null;
	}
}
