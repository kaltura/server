<?php

/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
class VendorCatalogItemPermissionName implements IKalturaPluginEnum, PermissionName
{
	const SYSTEM_ADMIN_CATALOG_ITEM_BASE = "reach.SYSTEM_ADMIN_CATALOG_ITEM_BASE";
	const SYSTEM_ADMIN_CATALOG_ITEM_MODIFY = "reach.SYSTEM_ADMIN_CATALOG_ITEM_MODIFY";

	public static function getAdditionalValues()
	{
		return array
		(
			'SYSTEM_ADMIN_CATALOG_ITEM_BASE' => self::SYSTEM_ADMIN_CATALOG_ITEM_BASE,
			'SYSTEM_ADMIN_CATALOG_ITEM_MODIFY' => self::SYSTEM_ADMIN_CATALOG_ITEM_MODIFY,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
