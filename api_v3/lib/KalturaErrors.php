<?php
class KalturaErrors extends APIErrors
{
	/**
	 * General Errors
	 *
	 */
	
	//
	const INTERNAL_SERVERL_ERROR = "INTERNAL_SERVERL_ERROR,Internal server error occured";
	
	//
	const MISSING_KS ="MISSING_KS,Missing KS, session not established";
	
	// %s - the ks string, %s - error code, %s - error description
	const INVALID_KS ="INVALID_KS,Invalid KS \"%s\", Error \"%s,%s\"";
	
	//
	const SERVICE_NOT_SPECIFIED = "SERVICE_NOT_SPECIFIED,Service name was not specified, please specify one";
	
	// %s - service name
	const SERVICE_DOES_NOT_EXISTS = "SERVICE_DOES_NOT_EXISTS,Service \"%s\" does not exists";
	
	//
	const ACTION_NOT_SPECIFIED = "ACTION_NOT_SPECIFIED,Action name was not specified, please specify one";
	
	// %s - action name, %s - service name
	const ACTION_DOES_NOT_EXISTS = "ACTION_DOES_NOT_EXISTS,Action \"%s\" does not exists for service \"%s\"";
	
	// %s - parameter name
	const MISSING_MANDATORY_PARAMETER = "MISSING_MANDATORY_PARAMETER,Missing parameter \"%s\"";
	
	// %s - invalid object type
	const INVALID_OBJECT_TYPE = "INVALID_OBJECT_TYPE,Invalid object type \"%s\"";
	
	// %s - enum value, %s - parameter name, %s - enum type
	const INVALID_ENUM_VALUE = "INVALID_ENUM_VALUE,Invalid enumeration value \"%s\" for parameter \"%s\", expecting enumeration type \"%s\"";
	
	// %s - partner id
	const INVALID_PARTNER_ID = "INVALID_PARTNER_ID,Invalid partner id \"%s\"";
	
	// %s - service , %s - action
	const INVALID_SERVICE_CONFIGURATION = "INVALID_SERVICE_CONFIGURATION,Invalid service configuration. Unknown service [%s:%s].";
	
	const PROPERTY_VALIDATION_CANNOT_BE_NULL =  "PROPERTY_VALIDATION_CANNOT_BE_NULL,The property \"%s\" cannot be NULL";
	
	const PROPERTY_VALIDATION_MIN_LENGTH = "PROPERTY_VALIDATION_MIN_LENGTH,The property \"%s\" must have a min length of %s characters";
	
	const PROPERTY_VALIDATION_MAX_LENGTH = "PROPERTY_VALIDATION_MAX_LENGTH,The property \"%s\" cannot have more than %s characters";
	
	const PROPERTY_VALIDATION_NUMERIC_VALUE = "PROPERTY_VALIDATION_NUMERIC_VALUE,The property \"%s\" must be numeric";
	
	const PROPERTY_VALIDATION_MIN_VALUE = "PROPERTY_VALIDATION_MIN_VALUE,The property \"%s\" must have a min value of %s";
	
	const PROPERTY_VALIDATION_MAX_VALUE = "PROPERTY_VALIDATION_MAX_VALUE,The property \"%s\" must have a max value of %s";
	
	const PROPERTY_VALIDATION_NOT_UPDATABLE = "PROPERTY_VALIDATION_NOT_UPDATABLE,The property \"%s\" cannot be updated";
	
	const PROPERTY_VALIDATION_ADMIN_PROPERTY = "PROPERTY_VALIDATION_ADMIN_PROPERTY,The property \"%s\" is updatable with admin session only";
	
	const INVALID_USER_ID = "INVALID_USER_ID,Invalid user id";
	
	const DATA_CENTER_ID_NOT_FOUND = "DATA_CENTER_ID_NOT_FOUND,There is no data center with id [%s]";
	
	/**
	 * Service Oriented Errors
	 *
	 */
	
	/**
	 * Media Service
	 */
	
	const ENTRY_ID_NOT_FOUND = "ENTRY_ID_NOT_FOUND,Entry id \"%s\" not found";
	
	const ENTRY_ID_NOT_REPLACED = "ENTRY_ID_NOT_REPLACED,Entry id \"%s\" not replaced";
	
	const ENTRY_REPLACEMENT_ALREADY_EXISTS = "ENTRY_REPLACEMENT_ALREADY_EXISTS,Entry already in replacement";
	
	const ENTRY_TYPE_NOT_SUPPORTED = "ENTRY_TYPE_NOT_SUPPORTED,Entry type \"%s\" not suppported";
	
	const RESOURCE_TYPE_NOT_SUPPORTED = "RESOURCE_TYPE_NOT_SUPPORTED,Resource type \"%s\" not suppported";
	
	const ENTRY_MEDIA_TYPE_NOT_SUPPORTED = "ENTRY_MEDIA_TYPE_NOT_SUPPORTED,Entry media type \"%s\" not suppported";
	
	const UPLOADED_FILE_NOT_FOUND_BY_TOKEN = "UPLOADED_FILE_NOT_FOUND_BY_TOKEN,The uploaded file was not found by the given token id, or was already used";
	
	const RECORDED_WEBCAM_FILE_NOT_FOUND = "RECORDED_WEBCAM_FILE_NOT_FOUND,The recorded webcam file was not found by the given token id, or was already used";
	
	const PERMISSION_DENIED_TO_UPDATE_ENTRY = "PERMISSION_DENIED_TO_UPDATE_ENTRY,User can update only the entries he own, otherwise an admin session must be used";
	
	const INVALID_RANK_VALUE = "INVALID_RANK_VALUE,Invalid rank value, rank should be between 1 and 5";
	
	const MAX_CATEGORIES_FOR_ENTRY_REACHED = "MAX_CATEGORIES_FOR_ENTRY_REACHED,Entry can be linked with a maximum of \"%s\" categories";

	const INVALID_ENTRY_SCHEDULE_DATES = "INVALID_ENTRY_SCHEDULE_DATES,Invalid entry schedule dates";
	
	const INVALID_ENTRY_STATUS = "INVALID_ENTRY_STATUS,Invalid entry status";
	
	const ENTRY_CANNOT_BE_FLAGGED = "ENTRY_CANNOT_BE_FLAGGED,Entry cannot be flagged";
	
	/**
	 * Notification Service
	 */
	
	const NOTIFICATION_FOR_ENTRY_NOT_FOUND = "NOTIFICATION_FOR_ENTRY_NOT_FOUND,Notification for entry id \"%s\" not found";
	
	/**
	 * Bulk Upload Service
	 */
	
	const BULK_UPLOAD_NOT_FOUND = "BULK_UPLOAD_NOT_FOUND,Bulk upload id \"%s\" not found";
	
	/**
	 * Widget Service
	 */
	
	const SOURCE_WIDGET_OR_UICONF_REQUIRED = "SOURCE_WIDGET_OR_UICONF_REQUIRED,SourceWidgetId or UiConfId id are required";
	
	const SOURCE_WIDGET_NOT_FOUND = "SOURCE_WIDGET_NOT_FOUND,Source widget id \"%s\" not found";
	
	/**
	 * UiConf Service
	 */
	const UICONF_ID_NOT_FOUND = "UICONF_ID_NOT_FOUND,Ui conf id \"%s\" not found";
	
	/**
	 * AccessControl Service
	 */
	const ACCESS_CONTROL_ID_NOT_FOUND = "ACCESS_CONTROL_ID_NOT_FOUND,Access control id \"%s\" not found";
	
	const MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED = "MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED,Max number of \"%s\" access controls was reached";
	
	const CANNOT_DELETE_DEFAULT_ACCESS_CONTROL = "CANNOT_DELETE_DEFAULT_ACCESS_CONTROL,Default access control cannot be deleted";
	
	/**
	 * ConversionProfile Service
	 */
	const CONVERSION_PROFILE_ID_NOT_FOUND = "CONVERSION_PROFILE_ID_NOT_FOUND,Conversion profile id \"%s\" not found";
	
	const INGESTION_PROFILE_ID_NOT_FOUND = "INGESTION_PROFILE_ID_NOT_FOUND,Ingestion profile id \"%s\" not found";
	
	const CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE = "CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE,Default conversion profile cannot be deleted";
	
	const CONVERSION_PROFILE_ASSET_PARAMS_NOT_FOUND = "CONVERSION_PROFILE_ASSET_PARAMS_NOT_FOUND,Conversion profile id \"%s\" asset params id \"%s\" not found";
	
	
	/**
	 * FlavorParams Service
	 */
	const FLAVOR_PARAMS_ID_NOT_FOUND = "FLAVOR_PARAMS_ID_NOT_FOUND,Flavor params id \"%s\" not found";
	
	const FLAVOR_PARAMS_NOT_FOUND = "FLAVOR_PARAMS_NOT_FOUND,Flavor params not found";
	
	const FLAVOR_PARAMS_DUPLICATE = "FLAVOR_PARAMS_DUPLICATE,Flavor params [%s] defined more than once";
	
	const FLAVOR_PARAMS_SOURCE_DUPLICATE = "FLAVOR_PARAMS_SOURCE_DUPLICATE,More than onc source flavor defined";
	
	/**
	 * FlavorAsset Service
	 */
	const FLAVOR_ASSET_ID_NOT_FOUND = "FLAVOR_ASSET_ID_NOT_FOUND,Flavor asset id \"%s\" not found";
	
	const FLAVOR_ASSET_ALREADY_EXISTS = "FLAVOR_ASSET_ALREADY_EXISTS,Flavor asset id \"%s\" already use flavor params id \"%s\"";
	
	const FLAVOR_ASSET_RECONVERT_ORIGINAL = "FLAVOR_ASSET_RECONVERT_ORIGINAL,Cannot reconvert original flavor asset";
	
	const ORIGINAL_FLAVOR_ASSET_IS_MISSING = "ORIGINAL_FLAVOR_ASSET_IS_MISSING,The original flavor asset is missing";
	
	const ORIGINAL_FLAVOR_ASSET_NOT_CREATED = "ORIGINAL_FLAVOR_ASSET_NOT_CREATED,The original flavor asset could not be created [%s]";
	
	const NO_FLAVORS_FOUND = "NO_FLAVORS_FOUND,No flavors found";
	
	/**
	 * ThumbAsset Service
	 */
	const THUMB_ASSET_ID_NOT_FOUND = "THUMB_ASSET_ID_NOT_FOUND,The Thumbnail asset id \"%s\" not found";
	
	const THUMB_ASSET_PARAMS_ID_NOT_FOUND = "THUMB_ASSET_ID_NOT_FOUND,The Thumbnail asset not found for params id \"%s\"";
	
	const THUMB_ASSET_IS_NOT_READY = "THUMB_ASSET_IS_NOT_READY,The thumbnail asset is not ready";
	
	const THUMB_ASSET_ALREADY_EXISTS = "THUMB_ASSET_ALREADY_EXISTS,Thumbnail asset id \"%s\" already use thumbnail params id \"%s\"";
	
	const THUMB_ASSET_DOWNLOAD_FAILED = "THUMB_ASSET_DOWNLOAD_FAILED,Fail to download thumbnain from URL \"%s\"";
	
	/**
	 * Category Service
	 */
	const CATEGORY_NOT_FOUND = "CATEGORY_NOT_FOUND,Category id \"%s\" not found";
	
	const PARENT_CATEGORY_NOT_FOUND = "PARENT_CATEGORY_NOT_FOUND,Parent category id \"%s\" not found";
	
	const DUPLICATE_CATEGORY = "DUPLICATE_CATEGORY,The category \"%s\" already exists";
	
	const PARENT_CATEGORY_IS_CHILD = "PARENT_CATEGORY_IS_CHILD,The parent category \"%s\" is one of the childs for category \"%s\"";
	
	const MAX_CATEGORY_DEPTH_REACHED = "MAX_CATEGORY_DEPTH_REACHED,Category can have a max depth of \"%s\" levels";
	
	const MAX_NUMBER_OF_CATEGORIES_REACHED = "MAX_NUMBER_OF_CATEGORIES_REACHED,Max number of \"%s\" categories was reached";
	
	const CATEGORIES_LOCKED = "CATEGORIES_LOCKED,Categories are locked, lock will be automatically released in \"%s\" seconds";
	
	/**
	 * Batch Service
	 */
	
	const SCHEDULER_HOST_CONFLICT = "SCHEDULER_HOST_CONFLICT, Scheduler id \"%s\" conflicts between hosts: \"%s\" and \"%s\"";
	
	const SCHEDULER_NOT_FOUND = "SCHEDULER_NOT_FOUND, Scheduler id \"%s\" not found";
	
	const WORKER_NOT_FOUND = "WORKER_NOT_FOUND, Worker id \"%s\" not found";
	
	const COMMAND_NOT_FOUND = "COMMAND_NOT_FOUND, Command id \"%s\" not found";
	
	const COMMAND_ALREADY_PENDING = "COMMAND_ALREADY_PENDING, Command already pending";
	
	const PARTNER_NOT_SET = "PARTNER_NOT_SET, Partner not set";
	
	/**
	 * Upload Service
	 */
	const INVALID_UPLOAD_TOKEN_ID = "INVALID_UPLOAD_TOKEN_ID,Invalid upload token id";
	
	const UPLOAD_PARTIAL_ERROR = "UPLOAD_PARTIAL_ERROR,File was uploaded partially";
	
	const UPLOAD_ERROR = "UPLOAD_ERROR,Upload failed";
	
	const UPLOADED_FILE_NOT_FOUND = "UPLOADED_FILE_NOT_FOUND,Uploaded file not found [%s]";
	
	const BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR,Unable to create file sync object for bulk upload csv";
	
	const BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR,Unable to create file sync object for bulk upload result";
	
	const BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR,Unable to create file sync object for flavor conversion";
	
	/**
	 * Upload Token Service
	 */
	const UPLOAD_TOKEN_NOT_FOUND = "UPLOAD_TOKEN_NOT_FOUND,Upload token not found";
	
	const UPLOAD_TOKEN_INVALID_STATUS_FOR_UPLOAD = "UPLOAD_TOKEN_INVALID_STATUS_FOR_UPLOAD,Upload token is in an invalid status for uploading a file, maybe the file was already uploaded";
	
	const UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY = "UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY,Upload token is in an invalid status for adding entry, maybe the a file was not uploaded or the token was used";
	
	const UPLOAD_TOKEN_CANNOT_RESUME = "UPLOAD_TOKEN_CANNOT_RESUME,Cannot resume the upload, original file was not found";
	
	const UPLOAD_TOKEN_RESUMING_NOT_ALLOWED = "UPLOAD_TOKEN_RESUMING_NOT_ALLOWED,Resuming not allowed when file size was not specified";
	
	const UPLOAD_TOKEN_RESUMING_INVALID_POSITION = "UPLOAD_TOKEN_RESUMING_INVALID_POSITION,Resuming not allowed after end of file";
	
	/*
	 * Partenrs service
	 * %s - the parent partner_id
	 */
	const NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD = "NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD,Partner id [%s] is not a VAR/GROUP, but is attempting to create child partner";	
	
	
	const INVALID_OBJECT_ID = "INVALID_OBJECT_ID,Invalid object id [%s]";
	
	const USER_NOT_FOUND = "USER_NOT_FOUND,User was not found";
		
	const USER_LOGIN_ALREADY_ENABLED = 'USER_LOGIN_ALREADY_ENABLED,User is already allowed to login';
	
	const USER_LOGIN_ALREADY_DISABLED = 'USER_LOGIN_ALREADY_DISABLED,User is already not allowed to login';
	
	const PROPERTY_VALIDATION_NO_UPDATE_PERMISSION = "PROPERTY_VALIDATION_NO_UPDATE_PERMISSION,Current user does not have permission to update property \"%s\"";

	const PROPERTY_VALIDATION_NO_INSERT_PERMISSION = "PROPERTY_VALIDATION_NO_INSERT_PERMISSION,Current user does not have permission to insert property \"%s\"";
	
	const USER_NOT_ADMIN = "USER_NOT_ADMIN,User [%s] is not admin";
	
	const ROLE_NAME_ALREADY_EXISTS = "ROLE_NAME_ALREADY_EXISTS,A role with the same name already exists";
	
	const PERMISSION_ITEM_NOT_FOUND = "PERMISSION_ITEM_NOT_FOUND,Permission item does not exist";
	
	/*
	 * syndication service
	 */
	const INVALID_XSLT = "INVALID_XSLT,Invalid xslt";
	
	const INVALID_XSLT_MISSING_TEMPLATE_RSS = "INVALID_XSLT_MISSING_TEMPLATE_RSS,Invalid xslt, missing rss's template tag with the following convention: xsl:template name=\"rssx\" match=\"/\"";
	
	const INVALID_XSLT_MISSING_TEMPLATE_ITEM = "INVALID_XSLT_MISSING_TEMPLATE_ITEM,Invalid xslt, missing item's template tag with the following convention: xsl:template name=\"item\" match=\"item\"";
	
	const INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM = "INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM,Invalid xslt, missing apply-template tag for item's template with the following convention: xsl:apply-templates name=\"item\"";
	
	/*
	 * file sync
	 */
	const FILE_DOESNT_EXIST = "FILE_DOESNT_EXIST,File doesnt exist";
	
	const STORAGE_PROFILE_ID_NOT_FOUND = "STORAGE_PROFILE_ID_NOT_FOUND,Storage profile id %s not found";
}