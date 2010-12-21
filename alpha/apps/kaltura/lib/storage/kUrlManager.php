<?php
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
	protected $protocol = StorageProfile::PLAY_FORMAT_HTTP;
	
	/**
	 * @var string
	 */
	protected $domain = null;
	
	/**
	 * @var int
	 */
	protected $storageProfileId = null;
	
	/**
	 * @param string $cdnHost
	 * @return kUrlManager
	 */
	public static function getUrlManagerByCdn($cdnHost)
	{
		$class = 'kUrlManager';
		
		$cdnHost = preg_replace('/https?:\/\//', '', $cdnHost);
		$params = null;
	
		$urlManagers = kConf::get('url_managers');
		if(isset($urlManagers[$cdnHost]))
		{
			$class = $urlManagers[$cdnHost]["class"];
			$params = @$urlManagers[$cdnHost]["params"];
		}
			
		KalturaLog::log("Uses url manager [$class]");
		return new $class(null, $params);
	}
	
	/**
	 * @param int $storageProfileId
	 * @return kUrlManager
	 */
	public static function getUrlManagerByStorageProfile($storageProfileId)
	{
		$class = 'kUrlManager';
		
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if($storageProfile && $storageProfile->getUrlManagerClass() && class_exists($storageProfile->getUrlManagerClass()))
			$class = $storageProfile->getUrlManagerClass();
			
		KalturaLog::log("Uses url manager [$class]");
		return new $class($storageProfileId);
	}
	
	public function __construct($storageProfileId = null, $params = null)
	{
		$this->storageProfileId = $storageProfileId;
		$this->params = $params ? $params : array();
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
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		if($fileSync->getObjectSubType() == entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return $fileSync->getSmoothStreamUrl()."/manifest";
			
		$url = $fileSync->getFilePath();
		$url = str_replace('\\', '/', $url);
	
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP &&
                        ($this->extention && strtolower($this->extention) != 'flv' ||
                        $this->containerFormat && strtolower($this->containerFormat) != 'flash video'))
			$url = "mp4:$url";
		
		return $url;
	}
	
	/**
	 * @param thumbAsset $thumbAsset
	 * @return string
	 */
	public function getThumbnailAssetUrl(thumbAsset $thumbAsset)
	{
		$thumbAssetId = $thumbAsset->getId();
		$partnerId = $thumbAsset->getPartnerId();
		$url = "/api_v3/service/thumbAsset/action/serve/partnerId/$partnerId/thumbAssetId/$thumbAssetId";
		
		return $url;
	}
	
	/**
	 * @param asset $asset
	 * @return string
	 */
	public function getAssetUrl(asset $asset)
	{
		if($asset instanceof thumbAsset)
			return $this->getThumbnailAssetUrl($asset);
			
		if($asset instanceof flavorAsset)
			return $this->getFlavorAssetUrl($asset);
			
		return null;
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $flavorAsset->getentry()->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		$flavorAssetId = $flavorAsset->getId();
		
		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());
	
		$url = "$partnerPath/serveFlavor/flavorId/$flavorAssetId";
		
		if($this->seekFromTime)
			$url .= "/seekFrom/$this->seekFromTime";
			
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";
		
                if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
		{
			$url .= '/forceproxy/true';
			if ($this->extention && strtolower($this->extention) != 'flv' ||
                        	$this->containerFormat && strtolower($this->containerFormat) != 'flash video')	
			{
				$url = "mp4:$url/name/$flavorAssetId.mp4";
			}
			else
			{
				$url .= "/name/$flavorAssetId.flv";
			}
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
		$urlManagers = kConf::get('url_managers');
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

}
