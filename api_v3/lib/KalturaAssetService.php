<?php

/**
 * Retrieve information and invoke actions on all asset types
 * Used as base class for all asset services
 *
 * @package api
 * @subpackage services
 */
abstract class KalturaAssetService extends KalturaBaseService
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
	 * Get asset by ID
	 * 
	 * @action get
	 * @param string $id
	 * @return KalturaAsset
	 */
	abstract public function getAction($id);
	
	/**
	* @param asset $asset
	* @param string $fileName
	* @param bool $forceProxy
	* @param int $version
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
	
	/**
	 * Action for manually exporting an asset
	 * @param string $assetId
	 * @param int $storageProfileId
	 * @throws KalturaErrors::INVALID_FLAVOR_ASSET_ID
	 * @throws KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::INTERNAL_SERVERL_ERROR
	 */
	protected function exportAction ( $assetId , $storageProfileId )
	{	    
	    $dbAsset = assetPeer::retrieveById($assetId);
	    if (!$dbAsset)
	    {
	        throw new KalturaAPIException(KalturaErrors::INVALID_FLAVOR_ASSET_ID, $assetId);
	    }
	    
	    $dbStorageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);	    
	    if (!$dbStorageProfile)
	    {
	        throw new KalturaAPIException(KalturaErrors::STORAGE_PROFILE_ID_NOT_FOUND, $storageProfileId);
	    }
	    
	    $exported = kStorageExporter::exportFlavorAsset($dbAsset, $dbStorageProfile);
	    
	    if ($exported !== true)
	    {
	        //TODO: implement export errors
	        throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
	    }
	    
	    return $this->getAction($assetId);
	}
}
