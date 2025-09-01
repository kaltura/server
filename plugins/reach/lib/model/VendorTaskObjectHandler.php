<?php

interface VendorTaskObjectHandler
{
	public static function shouldAddEntryVendorTask($taskObject, $vendorCatalogItem): bool;

	public static function shouldAddEntryVendorTaskByTaskObject($taskObject, $vendorCatalogItem, $reachProfile) : bool;

	public static function getTaskKuserId($taskObject): int;

	public static function getTaskPuserId($taskObject): string;

	public function getAbortStatusMessage($status): string;

	public static function getTaskObjectsByEventObject(BaseObject $object);

	public static function getTaskObjectById($taskObjectId);

	public static function hasRestrainingAdminTag($taskObject, $profileId): bool;

	public static function isFeatureTypeSupportedForTaskObject($taskObject, VendorCatalogItem $vendorCatalogItem): bool;

	public static function getTaskObjectType();
  
}
