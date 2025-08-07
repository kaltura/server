<?php

class HandlerFactory {

	/**
	 * @throws KalturaAPIException
	 */
	public static function getHandler($objectType): VendorTaskObjectHandler {
		switch($objectType)
		{
			case EntryObjectType::ASSET:
				return new AssetHandler();
			case EntryObjectType::ENTRY:
				return new EntryHandler();
			default:
				throw new KalturaAPIException(KalturaReachErrors::ENTRY_OBJECT_TYPE_NOT_SUPPORTED, $objectType);

		}
	}

	/**
	 * @throws KalturaAPIException
	 */
	public static function getHandlerByObject($object): VendorTaskObjectHandler
	{
		$objectType = $object instanceof AttachmentAsset ? EntryObjectType::ASSET : EntryObjectType::ENTRY;
		return HandlerFactory::getHandler($objectType);
	}

	/**
	 * @throws KalturaAPIException
	 * TEMP solution for fix HF
	 */
	public static function getHandlerById($objectId, $eventObject): VendorTaskObjectHandler
	{
		// Getting the $eventObject to avoid calling to the DB is not needed.
		// As currently the only case when we need to check if the object ID is asset is if the event object is AttachmentAsset
		$object = null;
		if ($eventObject instanceof AttachmentAsset)
			$object = assetPeer::retrieveById($objectId);
		$objectType = $object ? EntryObjectType::ASSET : EntryObjectType::ENTRY;
		return HandlerFactory::getHandler($objectType);
	}
}
