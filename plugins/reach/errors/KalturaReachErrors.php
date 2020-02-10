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
	
	const CATALOG_ITEM_NOT_ENABLED_FOR_ACCOUNT = "CATALOG_ITEM_NOT_ENABLED_FOR_ACCOUNT;ID;Catalog item with id provided [@ID@], is not enabled on account level";
	
	const CATALOG_ITEM_ONLY_HUMAN_ALLOWED = "CATALOG_ITEM_ONLY_HUMAN_ALLOWED;;Only human service type is allowed for given vendor catalog item";

	/* Reach Profile */

	const REACH_PROFILE_NOT_FOUND = "REACH_PROFILE_NOT_FOUND;ID;Reach profile with id provided not found [@ID@]";

	const DICTIONARY_LANGUAGE_DUPLICATION = "DICTIONARY_LANGUAGE_DUPLICATION;LANGUAGE;Vendor profile configuration error - not allowed duplicate dictionaries for language [@LANGUAGE@] ";

	const MAX_DICTIONARY_LENGTH_EXCEEDED = "MAX_DICTIONARY_LENGTH_EXCEEDED;LANGUAGE,LENGTH;Vendor profile configuration error - Dictionary for language [@LANGUAGE@] exceeded maximum length of  [@LENGTH@] characters";

	const INVALID_CREDIT_DATES = "INVALID_CREDIT_DATES;FROM,TO;Vendor profile configuration error - Invalid credit Dates - from:[@FROM@] to:[@TO@]";
	
	const INSUFFICIENT_CREDIT_TRANSFER = "INSUFFICIENT_CREDIT_TRANSFER;ID;Insufficent credit transfer for reach profile \"@ID@\". Can't set reach profile addon credit balance to negative. Please enter a valid amount";

	/* Entry Vendor Task */

	const ENTRY_VENDOR_TASK_SERVICE_GET_JOB_NOT_ALLOWED = "VENDOR_TASK_SERVICE__GET_JOB_NOT_ALLOWED;ID;Vendor task service 'Get Job' action not allowed. Vendor partner Type is not enabled on partner [@ID@]";

	const ENTRY_VENDOR_TASK_NOT_FOUND = "ENTRY_VENDOR_TASK_NOT_FOUND;ID;Entry vendor task item with id provided not found [@ID@]";

	const ENTRY_VENDOR_TASK_ACTION_NOT_ALLOWED = "ENTRY_VENDOR_TASK_ACTION_NOT_ALLOWED;ID,USER_ID;User id [@USER_ID@] is not allowed to do actions on entryVendorTask [@ID@]";
	
	const ENTRY_VENDOR_TASK_DUPLICATION = "ENTRY_VENDOR_TASK_DUPLICATION;ENTRY_ID,CATALOG_ITEM_ID,VERSION;Entry vendor task already exists for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@] and version [@VERSION@]";
	
	const EXCEEDED_MAX_CREDIT_ALLOWED = "EXCEEDED_MAX_CREDIT_ALLOWED;ENTRY_ID,CATALOG_ITEM_ID;Exceeded max credit allowed, task could not be added for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@]";

	const CREDIT_EXPIRED = "CREDIT_EXPIRED;ENTRY_ID,CATALOG_ITEM_ID;Credit cycle has expired, task could not be added for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@]";

	const CANNOT_APPROVE_NOT_MODERATED_TASK = "CANNOT_APPROVE_NOT_MODERATED_TASK;;Cannot approve task which is not pending moderation";
	
	const CANNOT_REJECT_NOT_MODERATED_TASK = "CANNOT_REJECT_NOT_MODERATED_TASK;;Cannot reject task which is not pending moderation";
	
	const CANNOT_ABORT_NOT_MODERATED_TASK = "CANNOT_ABORT_NOT_MODERATED_TASK;;Cannot abort task which is not pending moderation";
	
	const PARTNER_DATA_NOT_VALID_JSON_STRING = "PARTNER_DATA_NOT_VALID_JSON_STRING;;Partner data must be a vlaid json string";
	
	const CANNOT_UPDATE_STATUS_OF_TASK_WHICH_IS_IN_FINAL_STATE = "CANNOT_UPDATE_STATUS_OF_TASK_WHICH_IS_IN_FINAL_STATE;ID,OLD_STATUS,NEW_STATUS;Cannot update status from [@OLD_STATUS@] to [@NEW_STATUS@] for task with id [@ID@], since task is in final status";
	
	const ENTRY_TYPE_NOT_SUPPORTED = "ENTRY_TYPE_NOT_SUPPORTED;TYPE;Requesting tasks for entry type [@TYPE@] is not supported";
	
	const ENTRY_NOT_READY = "ENTRY_NOT_READY;;Ordering task is not allowed for entries which are not ins status ready";
	
	const CANNOT_EXTEND_ACCESS_KEY = "CANNOT_EXTEND_ACCESS_KEY;;Extending accessKey for non processing task is not allowed";
	
	const FAILED_EXTEND_ACCESS_KEY = "FAILED_EXTEND_ACCESS_KEY;;Extending accessKey operation failed";
	
	const TASK_NOT_CREATED = "TASK_NOT_CREATED;ENTRY_ID,CATALOG_ITEM_ID;Failed to create task for entry [@ENTRY_ID@] and catalog item [@CATALOG_ITEM_ID@]";
	
	/* Credit */
	
	const OVERAGE_CREDIT_CANNOT_BE_NEGATIVE = "OVERAGE_CREDIT_CANNOT_BE_NEGATIVE;;Overage credit value cannot be negative";
}
