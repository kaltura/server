<?php

class DeliveryProfileAkamaiSilverLight extends DeliveryProfileSilverLight {
	
	// doGetFlavorAssetUrl - Fully inherit from parent
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::doGetFileSyncUrl($fileSync);
		
		$serverUrl = $storage->getDeliveryIisBaseUrl();
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		
		if($fileSync->getObjectType() == FileSyncObjectType::ENTRY && $fileSync->getObjectSubType() == entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath);
		
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET && $fileSync->getObjectSubType() == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath);
		
		return parent::doGetFileSyncUrl($fileSync);
	}
	
	private function doGetServeIsmUrl(FileSync $fileSync, $partnerPath)
	{
		$serverUrl = myPartnerUtils::getIisHost($fileSync->getPartnerId(), "http");
	
		$path = $partnerPath.'/serveIsm/objectId/' . $fileSync->getObjectId() . '_' . $fileSync->getObjectSubType() . '_' . $fileSync->getVersion() . '.' . pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_EXTENSION) . '/manifest';
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $serverUrl, $matches))
		{
			$path = $matches[2] . $path;
		}
	
		$path = str_replace('//', '/', $path);
		return $path;
	}
}

