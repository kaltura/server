<?php
class myAssetUtils
{

	/**
	 * @param asset $asset
	 * @params entry $entry
	 * @return string
	 */
	public static function getFileName(entry $entry, flavorAsset $flavorAsset = null)
	{
		$fileExt = "";
		$fileBaseName = $entry->getName();
		if ($flavorAsset)
		{
			$flavorParams = $flavorAsset->getFlavorParams();
			if ($flavorParams)
				$fileBaseName = ($fileBaseName . " (" . $flavorParams->getName() . ")");

			$fileExt = $flavorAsset->getFileExt();
		}
		else
		{
			$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			if ($fileSync)
				$fileExt = $fileSync->getFileExt();
		}
	
		return array($fileBaseName, $fileExt);
	}

	public static function getAssetUrl(asset $asset, $servePlayManifest = false , $playManifestClientTag = null , $storageId = null)
	{
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if(!$partner)
			return null;
	
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = self::getExternalStorageUrl($partner, $asset, $syncKey, $servePlayManifest, $playManifestClientTag, $storageId);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;

		if($asset instanceof flavorAsset && $servePlayManifest)
		{
			$url =  requestUtils::getApiCdnHost() . $asset->getPlayManifestUrl($playManifestClientTag , $storageId);
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

	private static function getExternalStorageUrl(Partner $partner, asset $asset, FileSyncKey $key, $servePlayManifest = false , $playManifestClientTag = null , $storageId = null)
	{
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

			$url .= $asset->getPlayManifestUrl($playManifestClientTag ,$storageId); 
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
