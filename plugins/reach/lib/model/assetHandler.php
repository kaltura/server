<?php

class AssetHandler implements VendorTaskObjectHandler
{

	public static function shouldAddEntryVendorTask($taskObject, $vendorCatalogItem): bool
	{
			return true;
	}

	public static function shouldAddEntryVendorTaskByTaskObject($taskObject, $vendorCatalogItem, $reachProfile) : bool
	{
		if (!$vendorCatalogItem->isAssetSupported($taskObject))
		{
			KalturaLog::log("service {$vendorCatalogItem->getServiceFeature()} do not support asset {$taskObject->getId()}");
			return false;
		}
		return true;
	}

	public static function getTaskKuserId($taskObject): int
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$entryId = $taskObject->getEntryId();
			$entry = entryPeer::retrieveByPK($entryId);
			return $entry->getKuserId();
		}
		return $kuserId;
	}

	public static function getTaskPuserId($taskObject): string
	{
		$puserId = kCurrentContext::$ks_uid;
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$entryId = $taskObject->getEntryId();
			$entry = entryPeer::retrieveByPK($entryId);
			return $entry->getPuserId();
		}

		return $puserId;
	}

	public function getAbortStatusMessage($status): string
	{
		switch ($status)
		{
			case asset::ASSET_STATUS_DELETED:
				return "deleted";
			case asset::ASSET_STATUS_ERROR:
				return "error occurred";
			case asset::ASSET_STATUS_NOT_APPLICABLE:
				return "asset not applicable";
			default:
				return "invalid status provided";
		}
	}

	public static function getTaskObjectsByEventObject(BaseObject $object)
	{
		if ($object instanceof asset)
		{
			return [self::getTaskObjectById($object->getId())];
		}
		else if ($object instanceof categoryEntry)
		{
			return assetPeer::retrieveByEntryId($object->getEntryId());

		}
		return null;
	}

	public static function getTaskObjectById($taskObjectId)
	{
		return assetPeer::retrieveById($taskObjectId);
	}

	public static function hasRestrainingAdminTag($taskObject, $profileId): bool
	{
		return false;
	}

	public static function isFeatureTypeSupportedForObject($taskObject, VendorCatalogItem $vendorCatalogItem): bool
	{
		return $vendorCatalogItem->isAssetSupported($taskObject);
	}

	public static function getTaskObjectType()
	{
		return EntryObjectType::ASSET;
	}
}

