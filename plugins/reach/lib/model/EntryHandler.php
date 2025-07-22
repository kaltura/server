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

	public static function shouldAddEntryVendorTaskByObject($object, $vendorCatalogItem, $reachProfile): bool
	{
		if (!$vendorCatalogItem->isEntryTypeSupported($object->getType(), $object->getMediaType()))
		{
			KalturaLog::log("Entry of type [{$object->getType()}] is not supported by Reach");
			return false;
		}

		if (!kReachUtils::areFlavorsReady($object, $reachProfile))
		{
			KalturaLog::log("Not all flavor params IDs [{$reachProfile->getFlavorParamsIds()}] are ready yet");
			return false;
		}

		if($object->getParentEntryId())
		{
			KalturaLog::log("Entry [{$object->getId()}] is a child entry, entry vendor task object wont be created for it");
			return false;
		}

		if ($vendorCatalogItem->isEntryDurationExceeding($object))
		{
			KalturaLog::log("Entry [{$object->getId()}] is exceeding the catalogItem's limit, entry vendor task object wont be created for it");
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

	public static function getTaskObjectId(BaseObject $object)
	{
		return $object->getEntryId();
	}
  
	public static function retrieveObject($objectId): BaseObject
	{
		return entryPeer::retrieveByPK($objectId);
	}

	public static function hasRestrainingAdminTag($object, $profileId): bool
	{
		$reachRestrainAdminTag = kConf::get("reach_restrain_admin_tag", kConfMapNames::RUNTIME_CONFIG, null);
		if(in_array($reachRestrainAdminTag, $object->getAdminTagsArr()))
		{
			KalturaLog::log("Entry has reach restraining admin tag [$reachRestrainAdminTag]");
			return true;
		}
		return false;
	}

	public static function isFeatureTypeSupportedForObject($object, VendorCatalogItem $vendorCatalogItem): bool
	{
		$supportedType = $vendorCatalogItem->isEntryTypeSupported($object->getType(), $object->getMediaType());
		return !$vendorCatalogItem->isEntryDurationExceeding($object) && $supportedType;
	}
  
}
