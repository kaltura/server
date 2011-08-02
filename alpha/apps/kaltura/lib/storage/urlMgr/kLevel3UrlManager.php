<?php
class kLevel3UrlManager extends kUrlManager
{
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$entry = $flavorAsset->getentry();
		$partnerId = $entry->getPartnerId();
		$subpId = $entry->getSubpId();
		$flavorAssetId = $flavorAsset->getId();
		$flavorAssetVersion = $flavorAsset->getVersion();
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());

		$versionString = (!$flavorAssetVersion || $flavorAssetVersion == 1 ? '' : "/v/$flavorAssetVersion");
		$url = "$partnerPath/serveFlavor{$versionString}/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/$flavorAssetId.$this->extention";
					
		if($this->protocol != StorageProfile::PLAY_FORMAT_RTMP)
		{	
			$url .= '?novar=0';
				
			if ($entry->getSecurityPolicy())
			{
				$url = "/s$url";
				
				// set expire time in GMT hence the date("Z") offset
				$url .= "&nva=" . strftime("%Y%m%d%H%M%S", time() - date("Z") + 30);
				
				$name = isset($this->params['http_auth_param_name']) ? $this->params['http_auth_param_name'] : "h";
				$key = $this->params['http_auth_key'];
				$gen = $this->params['http_auth_gen'];
				$hash = $gen . substr(self::hmac('sha1', $key, $url), 0, 20);
				$url .= "&$name=$hash"; 
			}
		
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&start=' . $seekFromBytes;
		}
		else
		{
			$url = $this->tokenizeUrl($url);

			if($this->extention && strtolower($this->extention) != 'flv' ||
				$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
				$url = "mp4:$url";
		}
		
		$url = str_replace('\\', '/', $url);
		return $url;
	}

	protected function tokenizeUrl($url)
	{
		$name = isset($this->params['rtmp_auth_param_name']) ? $this->params['rtmp_auth_param_name'] : "h";
		$key = isset($this->params['rtmp_auth_key']) ? $this->params['rtmp_auth_key'] : false;
		$gen = isset($this->params['rtmp_auth_gen']) ? $this->params['rtmp_auth_gen'] : false;
		if ($name && $key && $gen)
		{
			$url .= "?$name=$gen" . substr(self::hmac('sha1', $key, str_replace('mp4:', '', $url)), 0, 20);
		}

		return $url;
	}

	/**
	* @param FileSync $fileSync
	* @return string
	*/
	public function getFileSyncUrl(FileSync $fileSync)
	{
		// get url from parent
		$url = parent::getFileSyncUrl($fileSync);
	    
		// if level3 tokenized url is used for rtmp, generated token string
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
			$url = $this->tokenizeUrl($url);

		return $url;
	}
}
