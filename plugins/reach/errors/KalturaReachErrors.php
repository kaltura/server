<?php

/**
 * @package plugins.reach
 * @subpackage api.errors
 */

class KalturaReachErrors implements kReachErrors
{
	/* Vendor Catalog Item*/
	
	const INVALID_CATALOG_ITEM_ID = "INVALID_CATALOG_ITEM_ID;ID;Invalid catalog item id [@ID@]";
	
	const CATALOG_ITEM_NOT_FOUND = "CATALOG_ITEM_NOT_FOUND;ID;Catalog item with id provided not found [@ID@]";
	
	const PARTNER_CATALOG_ITEM_NOT_FOUND = "PARTNER_CATALOG_ITEM_NOT_FOUND;ID;Partner catalog item with id provided not found [@ID@]";
	
	const VENDOR_PARTNER_ID_NOT_FOUND = "VENDOR_PARTNER_ID_NOT_FOUND;PARTNER_ID;Partner id provided [@PARTNER_ID@] not found";
	
	const PARTNER_NOT_VENDOR = "PARTNER_NOT_VENDOR;PARTNER_ID;Partner [@PARTNER_ID@] is not of type vendor";
	
	const VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME = "VENDOR_CATALOG_ITEM_DUPLICATE_SYSTEM_NAME;NAME;Vendor catalog item with system name [@NAME@] already exists.";

	/* Vendor Profile */

	const VENDOR_PROFILE_NOT_FOUND = "VENDOR_PROFILE_NOT_FOUND;ID;Vendor profile with id provided not found [@ID@]";

	/* Entry Vendor Task */

	const ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED = "VENDOR_TASK_SERVICE__GET_JOB_NOT_ALLOWED;ID;Vendor Task Service 'Get Job' action Not allowed. Vendor partner Type is not enabled on partner [@ID@]";

	const ENTRY_VENDOR_TASK_NOT_FOUND = "ENTRY_VENDOR_TASK_NOT_FOUND;ID;entry vendor task item with id provided not found [@ID@]";
}