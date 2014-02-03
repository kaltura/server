<?php
/**
 * @package Core
 * @subpackage storage.LimeLight
 */
class kLimeLightUrlManager extends kUrlManager
{
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		if($this->protocol != PlaybackProtocol::RTMP)
		{
			if (isset($this->params['http_auth_key']) && $this->params['http_auth_key'])
				$tokenizer = new kLimeLightUrlTokenizer();
				$tokenizer->setUrlPrefix($this->protocol . '://' . $this->domain);
				$tokenizer->setKey($this->params['http_auth_key']);
				return $tokenizer;
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
		$url = "/s$partnerPath/serveFlavor/entryId/".$flavorAsset->getEntryId()."{$versionString}/flavorId/$flavorAssetId";
		
		if($this->clipTo)
			$url .= "/clipTo/$this->clipTo";

		if($this->extention)
			$url .= "/name/a.$this->extention";
						
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol != PlaybackProtocol::RTMP)
		{
			$url .= '?novar=0';
			
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
