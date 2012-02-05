<?php
/**
 * @package Core
 * @subpackage storage.LimeLight
 */
class kLimeLightUrlManager extends kUrlManager
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
		$url = "/s$partnerPath/serveFlavor{$versionString}/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/$flavorAssetId.$this->extention";
						
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol != StorageProfile::PLAY_FORMAT_RTMP)
		{
			$url .= '?novar=0';
			$url .= '&e=' . (time() + 120);
			
			$key = $this->params['http_auth_key'];
			$fullUrl = $this->protocol . '://' . $this->domain . $url;
			$url .= '&h=' . md5($key . $fullUrl);
			
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$seekFromBytes = $this->getSeekFromBytes(kFileSyncUtils::getLocalFilePathForKey($syncKey));
			if($seekFromBytes)
				$url .= '&fs=' . $seekFromBytes;
		}
		else
		{
        		if($this->extention && strtolower($this->extention) != 'flv' ||
		                $this->containerFormat && strtolower($this->containerFormat) != 'flash video')
		                $url = "mp4:$url";
		}
		
		return $url;
	}
}
