<?php

class DeliveryProfileAkamaiSilverLight extends DeliveryProfileSilverLight {
	
	// doGetFlavorAssetUrl - Fully inherit from parent
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::doGetFileSyncUrl($fileSync);
		
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		$objectSubType = $fileSync->getObjectSubType();
		
		if($fileSync->getObjectType() == FileSyncObjectType::ENTRY && $objectSubType == kEntryFileSyncSubType::ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath, $storage);

		//To Remove - Until the migration process from asset sub type 3 to asset sub type 1 will be completed we need to support both formats
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET && $objectSubType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM)
			return $this->doGetServeIsmUrl($fileSync, $partnerPath, $storage);
		
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET && $objectSubType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET)
		{
			$asset = assetPeer::retrieveById($fileSync->getObjectId());
			if($asset->hasTag(assetParams::TAG_ISM_MANIFEST))
				return $this->doGetServeIsmUrl($fileSync, $partnerPath, $storage);	
		}
		
		return parent::doGetFileSyncUrl($fileSync);
	}
	
	private function doGetServeIsmUrl(FileSync $fileSync, $partnerPath, StorageProfile $storage = null)
	{
		$serverUrl = $this->getUrl();
		
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

