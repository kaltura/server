<?php

interface VendorTaskObjectHandler
{
	public static function shouldAddEntryVendorTask($object, $vendorCatalogItem): bool;

	public static function shouldAddEntryVendorTaskByObject($object, $vendorCatalogItem, $reachProfile) : bool;

	public static function getTaskKuserId($object): int;

	public static function getTaskPuserId($entryObject): string;

	public function getAbortStatusMessage($status): string;

	public static function getTaskObjectId(BaseObject $object);

	public static function retrieveObject($objectId): BaseObject;

	public static function hasRestrainingAdminTag($object, $profileId): bool;

	public static function isFeatureTypeSupportedForObject($object, VendorCatalogItem $vendorCatalogItem): bool;
}
