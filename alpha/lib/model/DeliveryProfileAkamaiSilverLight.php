<?php

class DeliveryProfileAkamaiSilverLight extends DeliveryProfileSilverLight {
	
	// doGetFlavorAssetUrl - Fully inherit from parent
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::doGetFileSyncUrl($fileSync);
		
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		
		if($fileSync->getObjectType() == FileSyncObjectType::ENTRY && $fileSync->getObjectSubType() == entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath, $storage);
		
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET && $fileSync->getObjectSubType() == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath, $storage);
		
		return parent::doGetFileSyncUrl($fileSync);
	}
	
	private function doGetServeIsmUrl(FileSync $fileSync, $partnerPath, StorageProfile $storage = null)
	{
		$serverUrl = myPartnerUtils::getIisHost($fileSync->getPartnerId(), "http");
		
		if($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE) {
			$path = $partnerPath;
			$path .= '/serveIsm/objectId/' . $fileSync->getObjectId () . '_' . $fileSync->getObjectSubType () . '_' . $fileSync->getVersion ();
			$path .= '.' . pathinfo ( $fileSync->getFilePath (), PATHINFO_EXTENSION ) . '/manifest';
		} else 
		{
			if ($storage) 
			{
				$serverUrl = $this->getUrl();
			}
			$path = '/'.$fileSync->getFilePath(). '/manifest';
		}
	
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $serverUrl, $matches))
		{
			$path = $matches[2] . $path;
		}
	
		$path = str_replace('//', '/', $path);
		return $path;
	}
}

