<?php
/**
 * Abstract subclass for representing an exportable asset
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
abstract class exportableAsset extends asset
{

	/**
	 * Should return an array of file syncs to test for exporting
	 * @return array
	 */
	abstract protected function getSyncKeysForExporting();

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
			if($this->requiredToExportAsset($externalStorage))
			{
				KalturaLog::info('Asset id ['.$this->getId().'] is required to export to profile ['.$externalStorage->getId().'] - setting status to [EXPORTING]');
				$newStatus = asset::ASSET_STATUS_EXPORTING;
				break;
			}
		}
		KalturaLog::info('Setting status to ['.$newStatus.']');
		$this->setStatus($newStatus);
	}

	private function requiredToExportAsset(StorageProfile $storage)
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

		$keys = $this->getSyncKeysForExporting(); 

		foreach ($keys as $key)
		{
			if($storage->shoudlExportFileSync($key))
			{
				return true;
			}
		}

		foreach ($keys as $key)
		{
			// check if asset is currently being exported to the remote storage
			if ($storage->isPendingExport($key))
			{
				KalturaLog::info('Asset id ['.$this->getId().'] is currently being exported to profile ['.$storage->getId().']');
				return true;
			}
		}
		return false;
	}
}