<?php

class AssetHandler implements VendorTaskObjectHandler
{

	public static function shouldAddEntryVendorTask($object, $vendorCatalogItem): bool
	{
			return true;
	}

	public static function shouldAddEntryVendorTaskByObject($object, $vendorCatalogItem, $reachProfile) : bool
	{
		if (!$vendorCatalogItem->isAssetSupported($object))
		{
			KalturaLog::log("service {$vendorCatalogItem->getServiceFeature()} do not support asset {$object->getId()}");
			return false;
		}
		return true;
	}

	public static function getTaskKuserId($object): int
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$entryId = $object->getEntryId();
			$entry = entryPeer::retrieveByPK($entryId);
			return $entry->getKuserId();
		}
		return $kuserId;
	}

	public static function getTaskPuserId($entryObject): string
	{
		$puserId = kCurrentContext::$ks_uid;
		if(kCurrentContext::$ks_partner_id <= PartnerPeer::GLOBAL_PARTNER)
		{
			$entryId = $entryObject->getEntryId();
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

	public static function getTaskObjectId(BaseObject $object)
	{
		return $object->getId();
	}

	public static function retrieveObject($objectId)
	{
		return assetPeer::retrieveById($objectId);
	}

	public static function hasRestrainingAdminTag($object, $profileId): bool
	{
		return false;
	}

	public static function isFeatureTypeSupportedForObject($object, VendorCatalogItem $vendorCatalogItem): bool
	{
		return $vendorCatalogItem->isAssetSupported($object);
	}

	public static function getTaskObjectType()
	{
		return EntryObjectType::ASSET;
	}
}

