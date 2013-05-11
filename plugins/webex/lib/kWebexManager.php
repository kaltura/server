<?php
class kWebexManager implements kObjectAddedEventConsumer
{
	const WEBEX_FILE_EXT = 'arf';
	
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null) 
	{
		/* @var $object flavorAsset */
		$flavorParams = assetParamsPeer::retrieveBySystemName(WebexPlugin::WEBEX_FLAVOR_PARAM_SYS_NAME);
		if (!$flavorParams)
			throw new APIException(APIErrors::OBJECT_NOT_FOUND);
		
		$asset = $object->copy();
		$asset->setFlavorParamsId($flavorParams->getId());
		$asset->setFromAssetParams($flavorParams);
		$asset->setStatus(flavorAsset::ASSET_STATUS_READY);
		$asset->setIsOriginal(0);
		$asset->setTags($flavorParams->getTags());
		$asset->incrementVersion();
		$asset->save();
		kFileSyncUtils::createSyncFileLinkForKey($asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET), $object->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET));
		$origFileSync = kFileSyncUtils::getLocalFileSyncForKey($object->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET));
		$asset->setSize(intval($origFileSync->getFileSize()/1000));		
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object) 
	{
		if ($object instanceof flavorAsset)
		{
			if ($object->getFileExt() == self::WEBEX_FILE_EXT && $object->getIsOriginal())
			{
				return true;
			}
		}
		
		return false;
	}

	
}