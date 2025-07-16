<?php

interface VendorTaskObjectHandler {
	public static function shouldAddEntryVendorTask($object, $vendorCatalogItem): bool;
	public static function getTaskKuserId($object): int;
	public static function getTaskPuserId($entryObject): string;
	public function getAbortStatusMessage($status): string;
	public static function retrieveObject($objectId);
}
