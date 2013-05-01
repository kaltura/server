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
		
		$asset = new flavorAsset();
		$asset->setassetParams($flavorParams->getId());
		$asset->setFileExt('arf');
		$asset->setStatus(flavorAsset::ASSET_STATUS_READY);
		$asset->setIsOriginal(0);
		$asset->save();
		
		kFileSyncUtils::createSyncFileLinkForKey($asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET), $object->getSyncKey(FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET));
		
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