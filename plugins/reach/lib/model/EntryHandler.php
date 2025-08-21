<?php

class EntryHandler implements VendorTaskObjectHandler
{
	public static function shouldAddEntryVendorTask($taskObject, $vendorCatalogItem): bool
	{

		/** @var $taskObject entry */
		//Check if the entry is temporary, if so, dont create the task
		if ($taskObject->getIsTemporary())
		{
			KalturaLog::debug("Entry [{$taskObject->getId()}] is temporary, entry vendor task object wont be created for it");
			return false;
		}

		//Check if static content and the catalog item is excluding static content, if so, dont create the task
		if (count($vendorCatalogItem->getAdminTagsToExcludeArray()) && array_intersect($vendorCatalogItem->getAdminTagsToExcludeArray(), $taskObject->getAdminTagsArr()))
		{
			KalturaLog::debug("Entry [{$taskObject->getId()}] has admin tags that are excluded by the catalog item, entry vendor task object wont be created for it");
			return false;
		}
		return true;
	}

	public static function shouldAddEntryVendorTaskByTaskObject($taskObject, $vendorCatalogItem, $reachProfile): bool
	{
		if (!$vendorCatalogItem->isEntryTypeSupported($taskObject->getType(), $taskObject->getMediaType()))
		{
			KalturaLog::log("Entry of type [{$taskObject->getType()}] is not supported by Reach");
			return false;
		}

		if (!kReachUtils::areFlavorsReady($taskObject, $reachProfile))
		{
			KalturaLog::log("Not all flavor params IDs [{$reachProfile->getFlavorParamsIds()}] are ready yet");
			return false;
		}

		if($taskObject->getParentEntryId())
		{
			KalturaLog::log("Entry [{$taskObject->getId()}] is a child entry, entry vendor task object wont be created for it");
			return false;
		}

		if ($vendorCatalogItem->isEntryDurationExceeding($taskObject))
		{
			KalturaLog::log("Entry [{$taskObject->getId()}] is exceeding the catalogItem's limit, entry vendor task object wont be created for it");
			return false;
		}
		return true;

	}

	public static function getTaskKuserId($taskObject): int
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if (kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
			return $taskObject->getKuserId();
		}
		return $kuserId;
	}

	public static function getTaskPuserId($taskObject): string
	{
		$puserId = kCurrentContext::$ks_uid;
		if (kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			//For automatic dispatched tasks make sure to set the entry creator user as the entry owner
			return $taskObject->getPuserId();
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

	public static function getTaskObjectsByEventObject(BaseObject $object)
	{
		return [self::getTaskObjectById($object->getEntryId())];
	}

	public static function getTaskObjectById($taskObjectId)
	{
		return entryPeer::retrieveByPK($taskObjectId);
	}

	public static function hasRestrainingAdminTag($taskObject, $profileId): bool
	{
		$reachRestrainAdminTag = kConf::get('reach_restrain_admin_tag', kConfMapNames::RUNTIME_CONFIG, null);
		if(!is_null($reachRestrainAdminTag) && in_array($reachRestrainAdminTag, $taskObject->getAdminTagsArr()))
		{
			KalturaLog::log("Entry has reach restraining admin tag [$reachRestrainAdminTag]");
			return true;
		}
		return false;
	}

	public static function isFeatureTypeSupportedForObject($taskObject, VendorCatalogItem $vendorCatalogItem): bool
	{
		$supportedType = $vendorCatalogItem->isEntryTypeSupported($taskObject->getType(), $taskObject->getMediaType());
		return !$vendorCatalogItem->isEntryDurationExceeding($taskObject) && $supportedType;
	}

	public static function getTaskObjectType()
	{
		return EntryObjectType::ENTRY;
	}
  
}
