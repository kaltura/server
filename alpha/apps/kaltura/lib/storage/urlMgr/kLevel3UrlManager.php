<?php
/**
 * @package Core
 * @subpackage storage.Level3
 */
class kLevel3UrlManager extends kUrlManager
{
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		switch ($this->protocol)
		{
		case PlaybackProtocol::HTTP:
   		    $name = isset($this->params['http_auth_param_name']) ? $this->params['http_auth_param_name'] : "h";
			$key = isset($this->params['http_auth_key']) ? $this->params['http_auth_key'] : false;
			$gen = isset($this->params['http_auth_gen']) ? $this->params['http_auth_gen'] : false;
   		    $expiryName = isset($this->params['http_auth_expiry_name']) ? $this->params['http_auth_expiry_name'] : "etime";
			$window = isset($this->params['http_auth_window']) ? $this->params['http_auth_window'] : 0;
			$entry = entryPeer::retrieveByPK($this->entryId);
			if ($entry && $entry->getSecurityPolicy())
				$window = 30;
			if ($name && $key !== false && $gen !== false)
				return new kLevel3UrlTokenizer($name, $key, $gen, false, $expiryName, $window);
			break;

		case PlaybackProtocol::RTMP:
		    $name = isset($this->params['rtmp_auth_param_name']) ? $this->params['rtmp_auth_param_name'] : "h";
		    $key = isset($this->params['rtmp_auth_key']) ? $this->params['rtmp_auth_key'] : false;
		    $gen = isset($this->params['rtmp_auth_gen']) ? $this->params['rtmp_auth_gen'] : false;
   		    $expiryName = isset($this->params['rtmp_auth_expiry_name']) ? $this->params['rtmp_auth_expiry_name'] : "etime";
		    $window = isset($this->params['rtmp_auth_window']) ? $this->params['rtmp_auth_window'] : 0;
		    if ($name && $key !== false && $gen !== false)
				return new kLevel3UrlTokenizer($name, $key, $gen, true, $expiryName, $window);
			break;

		case PlaybackProtocol::APPLE_HTTP:
			$name = isset($this->params['applehttp_auth_param_name']) ? $this->params['applehttp_auth_param_name'] : "h";
			$key = isset($this->params['applehttp_auth_key']) ? $this->params['applehttp_auth_key'] : false;
			$gen = isset($this->params['applehttp_auth_gen']) ? $this->params['applehttp_auth_gen'] : false;
			$expiryName = isset($this->params['applehttp_auth_expiry_name']) ? $this->params['applehttp_auth_expiry_name'] : "etime";
			$window = isset($this->params['applehttp_auth_window']) ? $this->params['applehttp_auth_window'] : 0;
			if ($name && $key !== false && $gen !== false)
				return new kLevel3UrlTokenizer($name, $key, $gen, true, $expiryName, $window);
			break;
		}
		return null;
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$entry = $flavorAsset->getentry();
		$partnerId = $entry->getPartnerId();
		$subpId = $entry->getSubpId();
		$flavorAssetId = $flavorAsset->getId();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());

		$versionString = $this->getFlavorVersionString($flavorAsset);
		$url = "$partnerPath/serveFlavor/entryId/".$flavorAsset->getEntryId()."{$versionString}/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/a.$this->extention";
					
		if($this->protocol != PlaybackProtocol::RTMP)
		{	
			$url .= '?novar=0';
				
			if ($entry->getSecurityPolicy())
			{
				$url = "/s$url";
			}
		
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&start=' . $seekFromBytes;
		}
		else
		{
			if($this->extention && strtolower($this->extention) != 'flv' ||
				$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
				$url = "mp4:".ltrim($url,'/');
		}
				
		$url = str_replace('\\', '/', $url);
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
			$url = str_replace('.flv','',$url);
		}
		
		return $url;
	}
}
