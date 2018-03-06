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
	
	const VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER = "VENDOR_CATALOG_ITEM_ALREADY_ENABLED_ON_PARTNER;ID,PARTNER_ID;Vendor catalog item with id [@ID@] already enabled for partner [@PARTNER_ID@]";

	/* Vendor Profile */

	const VENDOR_PROFILE_NOT_FOUND = "VENDOR_PROFILE_NOT_FOUND;ID;Vendor profile with id provided not found [@ID@]";

	const DICTIONARY_LANGUAGE_DUPLICATION = "DICTIONARY_LANGUAGE_DUPLICATION;LANGUAGE; Vendor profile configuration error - not allowed duplicate dictionaries for language [@LANGUAGE@] ";

	const MAX_DICTIONARY_LENGTH_EXCEEDED = "MAX_DICTIONARY_LENGTH_EXCEEDED;LANGUAGE,LENGTH; Vendor profile configuration error - Dictionary for language [@LANGUAGE@] exceeded maximum length of  [@LENGTH@] characters";

	/* Entry Vendor Task */

	const ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED = "VENDOR_TASK_SERVICE__GET_JOB_NOT_ALLOWED;ID;Vendor Task Service 'Get Job' action Not allowed. Vendor partner Type is not enabled on partner [@ID@]";

	const ENTRY_VENDOR_TASK_NOT_FOUND = "ENTRY_VENDOR_TASK_NOT_FOUND;ID;entry vendor task item with id provided not found [@ID@]";

	const ENTRY_VENDOR_TASK_ACTION_NOT_ALLOWED = "ENTRY_VENDOR_TASK_ACTION_NOT_ALLOWED;ID,USER_ID;user id [@USER_ID@] is not allowed to do acitons on entryVendorTask [@ID@]";
	
	const ENTRY_VENDOR_TASK_DUPLICATION = "ENTRY_VENDOR_TASK_DUPLICATION;ENTRY_ID,CATALOG_ITEM_ID,VERSION;entry vendor task already exists for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@] and version [@VERSION@]";
	
	const EXCEEDED_MAX_CREDIT_ALLOWED = "EXCEEDED_MAX_CREDIT_ALLOWED;ENTRY_ID,CATALOG_ITEM_ID;Exceeded max credit allowed, Task could not be added for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@]";
	
	const CANNOT_APPROVE_NOT_MODERATED_TASK = "CANNOT_APPROVE_NOT_MODERATED_TASK;;Cannot approve task which is not pending moderation";
	
	const CANNOT_REJECT_NOT_MODERATED_TASK = "CANNOT_REJECT_NOT_MODERATED_TASK;;Cannot reject task which is not pending moderation";
	
	const PARTNER_DATA_NOT_VALID_JSON_STRING = "PARTNER_DATA_NOT_VALID_JSON_STRING;;Partner data must be a vlaid json string";
	
	/* Credit */
	
	const CANNOT_SET_ALLOW_OVERAGE_CREDIT = "CANNOT_SET_ALLOW_OVERAGE_CREDIT;;Allow overage set to false but overage credit is set";
}