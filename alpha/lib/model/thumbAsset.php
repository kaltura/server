<?php
/**
 * Subclass for representing a row from the 'flavor_asset' table, used for thumb_assets
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class thumbAsset extends asset
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setFileExt('jpg');
		$this->setType(assetType::THUMBNAIL);
	}
	
	public function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = '/api_v3/index.php/service/thumbAsset/action/serve';
		$finalPath .= '/thumbAssetId/' . $this->getId();
		if($this->getVersion() > 1)
		{
			$finalPath .= '/v/' . $this->getVersion();
		}
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		$entry = $this->getentry();
		
		$partnerVersion = $partner->getCacheThumbnailVersion();
		$entryVersion = $entry->getCacheThumbnailVersion();
		
		$finalPath .= ($partnerVersion ? "/pv/$partnerVersion" : '');
		$finalPath .= ($entryVersion ? "/ev/$entryVersion" : '');
						
		return $finalPath;
	}

	public function keepOnEntryReplacement()
	{
		if($this->getentry()->getReplacementOptions()->getKeepManualThumbnails() && !$this->getFlavorParamsId()) {
			return true;
		}

		return false;
	}
	
	public function getThumbnailUrl(KSecureEntryHelper $securyEntryHelper, $storageId = null, KalturaThumbParams $thumbParams = null)
	{
		if ($thumbParams)
		{
			$assetUrl = $this->getDownloadUrlWithExpiry(84600);
			$assetParameters = KalturaRequestParameterSerializer::serialize($thumbParams, "thumbParams");
			$thumbnailUrl = $assetUrl . "?thumbParams:objectType=KalturaThumbParams&".implode("&", $assetParameters);
		}
			
		if($storageId)
			$thumbnailUrl = $this->getExternalUrl($storageId);
			
		$thumbnailUrl = $this->getDownloadUrl(true);
		
		/* @var $serverNode EdgeServerNode */
		$serverNode = $securyEntryHelper->shouldServeFromServerNode();
		if(!$serverNode)
			return $thumbnailUrl;
		
		$urlParts = explode("://", $thumbnailUrl);
		return $urlParts[0] . "://" . $serverNode->buildEdgeFullPath('http', null, null, assetType::THUMBNAIL) . $urlParts[1];
	}

	/**
	 * (non-PHPdoc)
	 * @see asset::setStatusLocalReady()
	 */
	public function setStatusLocalReady()
	{
		$newStatus = asset::ASSET_STATUS_READY;

		$externalStorages = StorageProfilePeer::retrieveExternalByPartnerId($this->getPartnerId());
		foreach($externalStorages as $externalStorage)
		{
			if($this->requiredToExportFlavor($externalStorage))
			{
				KalturaLog::info('Asset id ['.$this->getId().'] is required to export to profile ['.$externalStorage->getId().'] - setting status to [EXPORTING]');
				$newStatus = asset::ASSET_STATUS_EXPORTING;
				break;
			}
		}
		KalturaLog::info('Setting status to ['.$newStatus.']');
		$this->setStatus($newStatus);
	}

	private function requiredToExportFlavor(StorageProfile $storage)
	{
		// check if storage profile should affect the asset ready status
		if ($storage->getReadyBehavior() != StorageProfileReadyBehavior::REQUIRED)
		{
			// current storage profile is not required for asset readiness - skipping
			return false;
		}

		// check if export should happen now or wait for another trigger
		if (!$storage->triggerFitsReadyAsset($this->getEntryId())) {
			KalturaLog::info('Asset id ['.$this->getId().'] is not ready to export to profile ['.$storage->getId().']');
			return false;
		}

		// check if asset needs to be exported to the remote storage
		if (!$storage->shouldExportFlavorAsset($this, true))
		{
			KalturaLog::info('Should not export asset id ['.$this->getId().'] to profile ['.$storage->getId().']');
			return false;
		}

		$key = $this->getSyncKey(thumbAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);

		if ($storage->shoudlExportFileSync($key))
			return true;
		
		if ($storage->isPendingExport($key))
		{
			KalturaLog::info('Asset id ['.$this->getId().'] is currently being exported to profile ['.$storage->getId().']');
			return true;
		}
		return false;
	}
	
}