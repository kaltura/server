<?php

class kObjectDeleteHandler implements kObjectDeletedEventConsumer
{
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object) 
	{
		if($object instanceof entry)
			$this->entryDeleted($object);
			
		if($object instanceof uiConf)
			$this->uiConfDeleted($object);
			
		if($object instanceof BatchJob)
			$this->batchJobDeleted($object);
			
		if($object instanceof asset)
			$this->assetDeleted($object);
			
		return true;
	}

	/**
	 * @param string $id
	 * @param int $type
	 */
	protected function syncableDeleted($id, $type) 
	{
		$c = new Criteria();
		$c->add(FileSyncPeer::OBJECT_ID, $id);
		$c->add(FileSyncPeer::OBJECT_TYPE, $type);
		$c->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_PURGED, FileSync::FILE_SYNC_STATUS_DELETED), Criteria::NOT_IN);
		
		$fileSyncs = FileSyncPeer::doSelect($c);
		foreach($fileSyncs as $fileSync)
		{
			$key = kFileSyncUtils::getKeyForFileSync($fileSync);
			kFileSyncUtils::deleteSyncFileForKey($key);
		}
	}

	/**
	 * @param entry $entry
	 */
	protected function entryDeleted(entry $entry) 
	{
		$this->syncableDeleted($entry->getId(), FileSyncObjectType::ENTRY);
		
		// delete flavor assets
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entry->getId());
		$c->add(assetPeer::STATUS, asset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL);
		$c->add(assetPeer::DELETED_AT, null, Criteria::ISNULL);
		
		assetPeer::resetInstanceCriteriaFilter();
		$assets = assetPeer::doSelect($c);
		foreach($assets as $asset)
		{
			$asset->setStatus(asset::FLAVOR_ASSET_STATUS_DELETED);
			$asset->setDeletedAt(time());
			$asset->save();
		}
	
		$c = new Criteria();
		$c->add(flavorParamsOutputPeer::ENTRY_ID, $entry->getId());
		$c->add(flavorParamsOutputPeer::DELETED_AT, null, Criteria::ISNULL);
		$flavorParamsOutputs = flavorParamsOutputPeer::doSelect($c);
		foreach($flavorParamsOutputs as $flavorParamsOutput)
		{
			$flavorParamsOutput->setDeletedAt(time());
			$flavorParamsOutput->save();
		}
	}

	/**
	 * @param uiConf $uiConf
	 */
	protected function uiConfDeleted(uiConf $uiConf) 
	{
		$this->syncableDeleted($uiConf->getId(), FileSyncObjectType::UICONF);
	}

	/**
	 * @param BatchJob $batchJob
	 */
	protected function batchJobDeleted(BatchJob $batchJob) 
	{
		$this->syncableDeleted($batchJob->getId(), FileSyncObjectType::BATCHJOB);
	}

	/**
	 * @param asset $asset
	 */
	protected function assetDeleted(asset $asset) 
	{
		$this->syncableDeleted($asset->getId(), FileSyncObjectType::FLAVOR_ASSET);
	}
}
