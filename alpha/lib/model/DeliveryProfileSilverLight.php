<?php

class DeliveryProfileSilverLight extends DeliveryProfileVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kSilverLightManifestRenderer';
	
	public function serve()
	{
		$manifestInfo = $this->getSmoothStreamUrl();
		return $this->getRenderer(array($manifestInfo));
	}
	
	/**
	 * @return array
	 */
	protected function getSmoothStreamUrl()
	{
		
		if($this->deliveryAttributes->getManifestFileSync()->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE)
		{
			$urlPrefix = myPartnerUtils::getIisHost($this->deliveryAttributes->getPartnerId(), $this->deliveryAttributes->getMediaProtocol());
		}
		else if($this->deliveryAttributes->getStorageId())
		{
			$storageProfile = StorageProfilePeer::retrieveByPK($this->deliveryAttributes->getStorageId());
			$urlPrefix = $storageProfile->getDeliveryIisBaseUrl();
		}
	
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix .= '/';
	
		$this->initDeliveryDynamicAttribtues($this->params->getManifestFileSync());
		$url = $this->getFileSyncUrl($this->params->getManifestFileSync(), false);
		return $this->getFlavorAssetInfo($url, $urlPrefix);
		
	}
}

