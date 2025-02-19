<?php

/**
 * This class is an abstract implementation for VOD delivery profiles  
 */
abstract class DeliveryProfileVod extends DeliveryProfile {
	
	/**
	 * @var array
	 */
	protected $preferredFlavor = null;
	
	/**
	 * @var bool
	 */
	protected $isMultiAudio = false;
	
	/** -------------------
	 * Functionality 
	 * --------------------*/

	protected function getPlayServerUrl()
	{
		return '';
	}

	protected  function generatePlayServerUrl()
	{
		$prefix = '';
		if($this->getDynamicAttributes()->getUiConfId())
			$prefix .= '/uiConfId/'.$this->getDynamicAttributes()->getUiConfId();
		$sessionId = $this->getDynamicAttributes()->getSessionId();
		if($sessionId)
			$prefix .= '/sessionId/'.$sessionId;
		else
			$prefix .= '/sessionId/{sessionId}';

		return $prefix;
	}
	
	/**
	 * @param asset $flavorAsset
	 * @return string representing the basic url.
	 */
	protected function getBaseUrl(asset $flavorAsset) {
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $entry->getSubpId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);

		$url = "$partnerPath/serveFlavor/entryId/".$entry->getId();
		$url .= $this->getDynamicAttributes()->getUsePlayServer() ? $this->getPlayServerUrl() : '';
		$url .= $this->getDynamicAttributes()->getHasValidSequence() ? '/sequence/'.$this->getDynamicAttributes()->getSequence() : '';

		if ($entry->getType() == entryType::PLAYLIST || $entry->getType() == entryType::LIVE_CHANNEL ||
			($this->getDynamicAttributes()->getHasValidSequence() && $flavorAsset->getType() == assetType::FLAVOR))
		{
			$partner = $entry->getPartner();
			$entryVersion = $entry->getVersion();
			$partnerFlavorVersion = $partner->getCacheFlavorVersion();
			$entryFlavorVersion = $entry->getCacheFlavorVersion();

			$url .= ($entryVersion ? "/v/$entryVersion" : '') .
				($partnerFlavorVersion ? "/pv/$partnerFlavorVersion" : '').
				($entryFlavorVersion ? "/ev/$entryFlavorVersion" : '');

			if(!($flavorAsset->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)))
				$url .= '/flavorParamIds/' . $flavorAsset->getFlavorParamsId();
		}
		else
		{
			$url .= $this->getFlavorVersionString($flavorAsset);
			$url .= '/flavorId/' . $flavorAsset->getId();
		}

		if (($entry->getType() == entryType::PLAYLIST) && ($flavorAsset->getType() == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)))
			$url .= '/captions/' . $flavorAsset->getLanguage();

		$urlParams = $this->params->getUrlParams();

		$clipTo = self::extractClipTo($urlParams);
		if($clipTo)
		{
			$url = self::insertAfter($url, 'entryId', 'clipTo', $clipTo);
		}

		$url .= $urlParams;


		return $url;
	}

	protected static function extractClipTo(&$urlParams)
	{
		list($clipToPos, $endClipToValPos) = self::getKeyValPositions($urlParams, 'clipTo');

		if($clipToPos === false)
		{
			return false;
		}

		$clipToValPos = $clipToPos + strlen('/clipTo/');
		$clipTo = substr($urlParams, $clipToValPos, $endClipToValPos - $clipToValPos);

		$urlParams = substr($urlParams, 0, $clipToPos) . substr($urlParams, $endClipToValPos);

		return $clipTo;
	}

	protected static function getKeyValPositions($url, $key)
	{
		$startKeyPos = strpos($url,"/{$key}/");
		if ($startKeyPos === false )
		{
			return array(false, false);
		}

		$endValPos = strpos($url, '/', $startKeyPos + strlen("/{$key}/"));
		if ($endValPos === false)
		{
			$endValPos = strlen($url);
		}

		return array($startKeyPos, $endValPos);
	}

	public static function insertAfter($url, $afterKey, $key, $val)
	{
		list($keyPos, $endValPos) = self::getKeyValPositions($url, $afterKey);

		if ($keyPos !== false )
		{
			$url = substr($url, 0, $endValPos) . "/{$key}/" . $val . substr($url, $endValPos);
		}

		return $url;
	}

	/**
	 * @param asset $flavorAsset
	 * @return string representing the version string
	 */
	protected function getFlavorVersionString(asset $flavorAsset)
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
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = $this->getBaseUrl($flavorAsset);
		if($this->params->getClipTo())
		{
			$url = self::insertAfter($url, 'entryId', 'clipTo', $this->params->getClipTo());
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
		
		$addExtension = false;
		if ($this->getDynamicAttributes()->getAddThumbnailExtension())
		{
			$addExtension = true;
		}
		
		$url = "/api_v3/service/thumbAsset/action/serve/partnerId/$partnerId/thumbAssetId/$thumbAssetId" . ($addExtension ?  "/$thumbAssetId." . $thumbAsset->getFileExt() : "");
		$url .= '/version/' . $thumbAsset->getVersion();
	
		return $url;
	}
	
	public function getAssetUrl(asset $asset, $tokenizeUrl = true)
	{
		$url = null;
	
		if($asset instanceof thumbAsset)
		{
			$url = $this->doGetThumbnailAssetUrl($asset);
		}
		else
		{
			$url = $this->doGetFlavorAssetUrl($asset);

			$url = str_replace('\\', '/', $url);
			if ($tokenizeUrl)
			{
				$dpUrlPath = !is_null($this->getUrl()) ? parse_url($this->getUrl(), PHP_URL_PATH) : '';
				$url = rtrim($dpUrlPath,'/').'/'.ltrim($url,'/');
				$tokenizer = $this->getTokenizer();
				if ($tokenizer)
				{
					$scheme = '';
					if(!is_null($this->getDynamicAttributes()->getMediaProtocol()))
						$scheme = $this->getDynamicAttributes()->getMediaProtocol()."://";

					$hostName = $scheme.$this->getHostName();

					$url = $tokenizer->tokenizeSingleUrl($url, $hostName);
					kApiCache::disableCache();
				}
			}
		}
	
		return $url;
	}
	
	public function getFullAssetUrl(asset $asset, $tokenizeUrl = true) {
		$assetUrl = $this->getAssetUrl($asset, $tokenizeUrl);
		$hostName = $this->getHostName();
		
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if($partner)
		{
			$defaultDeliveryCode = $partner->getDefaultDeliveryCode(); 
			if($defaultDeliveryCode !== false)
				$hostName = str_replace("{deliveryCode}", $defaultDeliveryCode, $hostName);
		}
		
		return $hostName . $assetUrl;
	}
	
	protected function addSeekFromBytes($flavorAsset, $url, $prefix) {
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
		if($seekFromBytes)
			$url .= '&' . $prefix . '=' . $seekFromBytes;
		return $url;
	}
	
	/**
	 * @return int
	 */
	protected function getSeekFromBytes($path)
	{
		if($this->params->getSeekFromTime() <= 0)
			return null;
	
		$flvWrapper = new myFlvHandler($path);
		if(!$flvWrapper->isFlv())
			return null;
	
		$audioOnly = false;
		if($flvWrapper->getFirstVideoTimestamp() < 0 )
			$audioOnly = true;
	
		list ( $bytes , $duration ,$firstTagByte , $toByte ) = $flvWrapper->clip(0, -1, $audioOnly);
		list ( $bytes , $duration ,$fromByte , $toByte, $seekFromTimestamp ) = $flvWrapper->clip($this->params->getSeekFromTime(), -1, $audioOnly);
		$seekFromBytes = myFlvHandler::FLV_HEADER_SIZE + $flvWrapper->getMetadataSize($audioOnly) + $fromByte - $firstTagByte;
	
		return $seekFromBytes;
	}
	
	public function getFullFileSyncUrl(FileSync $fileSync, $tokenizeUrl = true) {
		$url = $this->getFileSyncUrl($fileSync, $tokenizeUrl);
		return $this->getHostName() . $url;
	}
	
	/**
	 * @param FileSync $fileSync
	 * @param bool $tokenizeUrl
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync, $tokenizeUrl = true)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		$url = $this->doGetFileSyncUrl($fileSync);
		$url = str_replace('\\', '/', $url);
		if ($tokenizeUrl)
		{
			$tokenizer = $this->getTokenizer();
			if ($tokenizer)
			{
			    if (strpos($url, "://") === false)
			        $url = "/".ltrim($url,"/");

				$url = $tokenizer->tokenizeSingleUrl($url, $this->getUrlPrefix());
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
		$url = $fileSync->getFilePath();
		return $url;
	}
	
	/** -------------------
	 * 		Serve
	 * --------------------*/
	public function buildServeFlavors() 
	{
		return array();
	}
	
	/**
	 * @param bool $oneOnly
	 * @return array
	 */
	protected function buildHttpFlavorsArray()
	{
		$flavors = array();
		foreach($this->params->getflavorAssets() as $flavorAsset)
		{
			/* @var $flavorAsset asset */
			$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
			if ($httpUrl)
			{
				$flavors[] = $httpUrl;
			}
		}
		return $flavors;
	}
	
	protected function getUrlPrefix()
	{
		return $this->url;
	}
	
	/**
	 * @param asset $flavorAsset
	 * @return array
	 */
	protected function getFlavorHttpUrl(asset $flavorAsset)
	{
		if ($this->params->getStorageId())
		{
			$storage = StorageProfilePeer::retrieveByPK($this->params->getStorageId());
			if(!$storage->getExportPeriodically())
			{
				KalturaLog::debug('Storage url is generated as external url');
				return $this->getExternalStorageUrl($flavorAsset);
			}
		}
			
		$this->initDeliveryDynamicAttributes(null, $flavorAsset);
		$url = $this->getAssetUrl($flavorAsset, false);
		if ($this->params->getFormat() == PlaybackProtocol::RTSP)
		{
			// the host was already added by the url manager
			return $this->getFlavorAssetInfo($url, '', $flavorAsset);
		}
	
		$urlPrefix = '';
		if (strpos($url, "/") === 0)
		{
			$flavorSizeKB = $flavorAsset->getSize();
			if ($flavorSizeKB > kConf::get("max_file_size_downloadable_from_cdn_in_KB"))
				KalturaLog::log("flavor size $flavorSizeKB > max_file_size_downloadable_from_cdn_in_KB, deliveryProfileId=".$this->getId()." url=".$this->getUrl()." flavorId=".$flavorAsset->getId()." flavorExt=".$flavorAsset->getFileExt());
			$urlPrefix = $this->getUrlPrefix();
		}
	
		$urlPrefix = preg_replace('/^https?:\/\//', '', $urlPrefix);
		$url = preg_replace('/^https?:\/\//', '', $url);
	
		if ($urlPrefix)
		{
			$urlPrefix = $this->params->getMediaProtocol() . '://' . $urlPrefix;
			$urlPrefix = rtrim($urlPrefix, "/") . "/";
		}
		else
		{
			$url = $this->params->getMediaProtocol() . '://' . $url;
		}
	
		$url = ltrim($url, "/");
	
		return $this->getFlavorAssetInfo($url, $urlPrefix, $flavorAsset);
	}
		
	/**
	 * @param asset $flavorAsset
	 * @param FileSyncKey $key
	 * @return array
	 */
	protected function getExternalStorageUrl(asset $flavorAsset)
	{
		$remoteFileSyncs = $this->params->getRemoteFileSyncs();
		$fileSync = $remoteFileSyncs[$flavorAsset->getId()];
	
		$this->initDeliveryDynamicAttributes($fileSync, $flavorAsset);
		$url = $this->getFileSyncUrl($fileSync, false);
		$url = ltrim($url, "/");
	
		$urlPrefix = '';
		if (strpos($url, "://") === false) {
			$urlPrefix = $this->getUrlPrefix();
			$url = "/".$url;
		}
	
		return $this->getFlavorAssetInfo($url, $urlPrefix, $flavorAsset);
	}
	
	/**
	 *
	 * Private function which compares 2 flavors in order to sort an array.
	 * If a flavor's width and height parameters are equal to 0, it is
	 * automatically moved down the list so the player will not start playing it by default.
	 * @param array $flavor1
	 * @param array $flavor2
	 */
	protected function flavorCmpFunction ($flavor1, $flavor2)
	{
		// move the caption flavors to the end
		$isFlavor1Caption = $flavor1['type'] == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION);
		$isFlavor2Caption = $flavor2['type'] == CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION);
		
		if($isFlavor1Caption != $isFlavor2Caption)
		{
			if ($isFlavor1Caption)
			{
				return 1;
			}
			else
			{
				return -1;
			}
		}
		
		if($isFlavor1Caption)
		{
			return $flavor1['index'] - $flavor2['index'];
		}
		
		// move the audio flavors to the end unless we have multi audio stream which in this case they should be at the beginning
		$isAudio1 = $flavor1['height'] == 0 && $flavor1['width'] == 0;
		$isAudio2 = $flavor2['height'] == 0 && $flavor2['width'] == 0;
		
		if ($isAudio1 != $isAudio2)
		{
			if ($isAudio1)
			{
				return $this->isMultiAudio ? -1 : 1;
			}
			else 
			{
				return $this->isMultiAudio ? 1 : -1;
			}
		}
		
		//Move all Dolby audio flavors to the beginning of the audio flavors list
		if($isAudio1 == true)
		{
			return $this->compareAudio($flavor1, $flavor2);
		}
	
		// if a preferred bitrate was defined place it first
		if ($this->preferredFlavor == $flavor2)
		{
			return 1;
		}
		if ($this->preferredFlavor == $flavor1)
		{
			return -1;
		}
	
		// sort the flavors in ascending bitrate order
		if ($flavor1['bitrate'] > $flavor2['bitrate'])
		{
			return 1;
		}
		
		//If bitrates are equal return the first flavor to maintain the order of the original array
		//(make order compatible with php 7)
		return $flavor1['index'] - $flavor2['index'];
	}
	
	private function compareAudio($flavor1, $flavor2)
	{
		$audioCodec1 = $flavor1['audioCodec'];
		$audioCodec2 = $flavor2['audioCodec'];
		
		$isDefault1 = $flavor1['defaultAudio'];
		$isDefault2 = $flavor2['defaultAudio'];
		
		$dolbyAudioCodecList = array('ec-3','ac-3');
		$audioPriority = array('ec-3' => 2, 'ac-3' => 1);
		
		if($isDefault1 != $isDefault2)
		{
			if($isDefault1 && !in_array($audioCodec1, $dolbyAudioCodecList) && in_array($audioCodec2, $dolbyAudioCodecList)) 
			{
				return 1;
			}
			
			if($isDefault2 && !in_array($audioCodec2, $dolbyAudioCodecList) && in_array($audioCodec1, $dolbyAudioCodecList))
			{
				return -1;
			}
			
			return $isDefault1 ? -1 : 1;
		}
		
		//If both audio codec's are dolby prioritize them based on the audioPriority array
		if(in_array($audioCodec1, $dolbyAudioCodecList) && in_array($audioCodec2, $dolbyAudioCodecList) && ($audioPriority[$audioCodec2] != $audioPriority[$audioCodec1]))
		{
			return $audioPriority[$audioCodec2] - $audioPriority[$audioCodec1];
		}
		
		if(in_array($audioCodec1, $dolbyAudioCodecList))
			return -1;
		
		if(in_array($audioCodec2, $dolbyAudioCodecList))
			return 1;
		
		return $flavor1['index'] - $flavor2['index'];
	}
	
	/**
	 * @param array $flavors
	 * @return array
	 */
	protected function sortFlavors($flavors)
	{
		$i = 0;
		foreach ($flavors as &$currFlavor)
		{
			$currFlavor['index'] = $i;
			$i++;
		}
		
		$this->preferredFlavor = null;
		$minBitrateDiff = PHP_INT_MAX;
	
		if (!is_null($this->params->getPreferredBitrate()))
		{
			foreach ($flavors as $flavor)
			{
				if ($flavor['height'] == 0 && $flavor['width'] == 0)
					continue;		// audio flavor
	
				$bitrateDiff = abs($flavor['bitrate'] - $this->params->getPreferredBitrate());
				if (!$this->preferredFlavor || $bitrateDiff < $minBitrateDiff)
				{
					$this->preferredFlavor = $flavor;
					$minBitrateDiff = $bitrateDiff;
				}
			}
		}

		uasort($flavors, array($this,'flavorCmpFunction'));

		return array_values($flavors);
	}

	public function setDynamicAttributes(DeliveryProfileDynamicAttributes $params) {
		parent::setDynamicAttributes($params);
		if (is_null($this->params->getUsePlayServer()))
		{
			$this->params->setUsePlayServer($this->getAdStitchingEnabled());
		}
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes such as mediaProtocol, flv support, etc..
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		/* @var $entry Baseentry */
		$entry = $deliveryAttributes->getEntry();
		if ($entry && $entry->getType() === entryType::LIVE_STREAM && !$this->getSimuliveSupport())
		{
			return self::DYNAMIC_ATTRIBUTES_NO_SUPPORT;
		}
		return parent::supportsDeliveryDynamicAttributes($deliveryAttributes);
	}
}

