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
		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());
	
		$url = "$partnerPath/serveFlavor/flavorId/$flavorAssetId";
		
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
				
				$secret = kConf::get("level3_authentication_key");
				$hash = "0" . substr(self::hmac('sha1', $secret, $url), 0, 20);
				$url .= "&h=$hash"; 
			}
		
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&start=' . $seekFromBytes;
		}
		else
		{
			$url .= '/forceproxy/true';
		}
		
		$url = str_replace('\\', '/', $url);
		return $url;
	}
}