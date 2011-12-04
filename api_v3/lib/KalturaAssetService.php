<?php

/**
 * Retrieve information and invoke actions on all asset types
 * Used as base class for all asset services
 *
 * @package api
 * @subpackage services
 */
class KalturaAssetService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new conversionProfile2Peer());
		parent::applyPartnerFilterForClass(new assetParamsOutputPeer());
		parent::applyPartnerFilterForClass(new assetPeer());
		parent::applyPartnerFilterForClass(new assetParamsPeer());
	}
	
	/**
	* @param asset $asset
	* @param string $fileName
	* @param bool $forceProxy
	* @throws KalturaErrors::FILE_DOESNT_EXIST
	*/
	protected function serveAsset(asset $asset, $fileName, $forceProxy = false, $version = null)
	{
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $version);
		
		$fileSync = null;
		$serveRemote = false;
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		
		switch($partner->getStorageServePriority())
		{
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$serveRemote = true;
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if(!$fileSync)
					throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);
				
				break;
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if($fileSync)
					$serveRemote = true;
				
				break;
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
				$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
				if($fileSync)
					break;
					
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if(!$fileSync)
					throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);
				
				$serveRemote = true;
				break;
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
				if(!$fileSync)
					throw new KalturaAPIException(KalturaErrors::FILE_DOESNT_EXIST);
				
				break;
		}
		
		if($serveRemote && $fileSync)
		{
			header("Location: " . $fileSync->getExternalUrl());
			die;
		}
		
		return $this->serveFile($asset, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $fileName, $forceProxy);
	}
}
