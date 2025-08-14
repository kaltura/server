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
	public static function getHandlerAutomaticFlow($taskObjectType, $object): VendorTaskObjectHandler | null
	{
		if ($taskObjectType == EntryObjectType::ENTRY || $object instanceof asset)
		{
			return HandlerFactory::getHandler($taskObjectType);
		}
		return null;
	}
}
