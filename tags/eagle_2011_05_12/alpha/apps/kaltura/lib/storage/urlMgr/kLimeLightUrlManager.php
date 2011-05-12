<?php
class kLimeLightUrlManager extends kUrlManager
{
	protected function getMediaVault()
	{
		return kConf::get("limelight_madiavault_password");
	}
	
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
	
		$url = "/s$partnerPath/serveFlavor/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/$flavorAssetId.$this->extention";
						
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol != StorageProfile::PLAY_FORMAT_RTMP)
		{
			$url .= '?novar=0';
			$url .= '&e=' . (time() + 120);
			
			$secret = $this->getMediaVault();
			$fullUrl = $this->protocol . '://' . $this->domain . $url;
			$url .= '&h=' . md5($secret . $fullUrl);
			
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&fs=' . $seekFromBytes;
		}
		else
		{
			$url .= '/forceproxy/true';
		}
		
		return $url;
	}
}