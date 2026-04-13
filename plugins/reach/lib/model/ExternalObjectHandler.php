<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class ExternalObjectHandler implements VendorTaskObjectHandler
{

	public static function shouldAddEntryVendorTask($taskObject, $vendorCatalogItem): bool
	{
		return true;
	}

	public static function shouldAddEntryVendorTaskByTaskObject($taskObject, $vendorCatalogItem, $reachProfile): bool
	{
		return true;
	}

	public static function getTaskKuserId($taskObject): int
	{
		return kCurrentContext::getCurrentKsKuserId();
	}

	public static function getTaskPuserId($taskObject): string
	{
		return kCurrentContext::$ks_uid;
	}

	public function getAbortStatusMessage($status): string
	{
		return "external object task aborted";
	}

	public static function getTaskObjectsByEventObject(BaseObject $object)
	{
		return null;
	}

	public static function getTaskObjectById($taskObjectId)
	{
		return new ExternalTaskObject($taskObjectId);
	}

	public static function hasRestrainingAdminTag($taskObject, $profileId): bool
	{
		return false;
	}

	public static function isFeatureTypeSupportedForTaskObject($taskObject, VendorCatalogItem $vendorCatalogItem): bool
	{
		return true;
	}

	public static function getTaskObjectType()
	{
		return EntryObjectType::EXTERNAL_OBJECT;
	}
}
