<?php

class EntryHandler implements VendorTaskObjectHandler
{
	public static function shouldAddEntryVendorTask($object, $vendorCatalogItem): bool
	{

		/** @var $object entry */
		//Check if the entry is temporary, if so, dont create the task
		if ($object->getIsTemporary())
		{
			KalturaLog::debug("Entry [{$object->getId()}] is temporary, entry vendor task object wont be created for it");
			return false;
		}

		//Check if static content and the catalog item is excluding static content, if so, dont create the task
		if (count($vendorCatalogItem->getAdminTagsToExcludeArray()) && array_intersect($vendorCatalogItem->getAdminTagsToExcludeArray(), $object->getAdminTagsArr()))
		{
			KalturaLog::debug("Entry [{$object->getId()}] has admin tags that are excluded by the catalog item, entry vendor task object wont be created for it");
			return false;
		}
		return true;
	}

	public static function getTaskKuserId($object): int
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if (kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
			return $object->getKuserId();
		}
		return $kuserId;
	}

	public static function getTaskPuserId($entryObject): string
	{
		$puserId = kCurrentContext::$ks_uid;
		if (kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
			return $entryObject->getPuserId();
		}
		return $puserId;
	}

	public function getAbortStatusMessage($status): string
	{
		switch ($status)
		{
			case entryStatus::DELETED:
				return "deleted";
			case entryStatus::ERROR_CONVERTING:
				return "error occurred while converting";
			case entryStatus::ERROR_IMPORTING:
				return "error occurred while importing";
			default:
				return "invalid status provided";
		}

	}

	public static function retrieveObject($objectId): BaseObject
	{
		return entryPeer::retrieveByPK($objectId);
	}
}
