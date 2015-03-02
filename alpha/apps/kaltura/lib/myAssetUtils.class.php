<?php
class myAssetUtils
{

	/**
	 * @param asset $asset
	 * @param kMrssParameters $mrssParams
	 * @return string
	 */
	public static function getAssetUrl(asset $asset, kMrssParameters $mrssParams = null)
	{
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if(!$partner)
			return null;
	
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = self::getExternalStorageUrl($partner, $asset, $syncKey, $mrssParams);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		if($asset instanceof flavorAsset && $mrssParams && $mrssParams->getServePlayManifest())
		{
			$url =  requestUtils::getApiCdnHost() . $asset->getPlayManifestUrl($mrssParams->getPlayManifestClientTag(), $mrssParams->getStorageId());
		}
		else
		{
			$urlManager = DeliveryProfilePeer::getDeliveryProfile($asset->getEntryId());
			if($asset instanceof flavorAsset)
				$urlManager->initDeliveryDynamicAttributes(null, $asset);
			
			$url = $urlManager->getFullAssetUrl($asset);
		}
		
		$url = preg_replace('/^https?:\/\//', '', $url);
			
		return 'http://' . $url;
	}

	private static function getExternalStorageUrl(Partner $partner, asset $asset, FileSyncKey $key, kMrssParameters $mrssParams = null)
	{
		$storageId = null;
		$servePlayManifest = false;
		
		if($mrssParams)
		{
			$storageId = $mrssParams->getStorageId();
			$servePlayManifest = $mrssParams->getServePlayManifest();
		}
		
		if(!$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if(is_null($storageId) && $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageId);
		if(!$fileSync)
			return null;
			
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		if($servePlayManifest)
		{
			$deliveryProfile = DeliveryProfilePeer::getRemoteDeliveryByStorageId($storageId, $asset->getEntryId(), PlaybackProtocol::HTTP, "https");
			
			if (is_null($deliveryProfile))
				$url = infraRequestUtils::PROTOCOL_HTTP . "://" . kConf::get("cdn_api_host");
			else
				$url = requestUtils::getApiCdnHost();
	
			$url .= $asset->getPlayManifestUrl($mrssParams->getPlayManifestClientTag(), $storage->getId());
		}
		else
		{
			$urlManager = DeliveryProfilePeer::getRemoteDeliveryByStorageId($fileSync->getDc(), $asset->getEntryId());
			if($urlManager) {
				$dynamicAttrs = new DeliveryProfileDynamicAttributes();
				$dynamicAttrs->setFileExtension($asset->getFileExt());
				$urlManager->setDynamicAttributes($dynamicAttrs);
				
				$url = rtrim($urlManager->getUrl(),'/') . '/' . ltrim($urlManager->getFileSyncUrl($fileSync),'/');
			} else {
				KalturaLog::debug("Couldn't determine delivery profile for storage id");
				$url = null;
			}
		}
		
		return $url;
	}
}