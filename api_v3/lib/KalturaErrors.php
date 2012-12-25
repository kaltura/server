<?php
/**
 * @package api
 * @subpackage errors
 */
class KalturaErrors extends APIErrors
{
	/**
	 * General Errors
	 *
	 */
	
	//
	const INTERNAL_SERVERL_ERROR = "INTERNAL_SERVERL_ERROR,Internal server error occured";
	
	// should be used for internal actions only
	const INTERNAL_SERVERL_ERROR_DEBUG = "INTERNAL_SERVERL_ERROR,Internal server error occured \"%s\"";
	
	//
	const MISSING_KS ="MISSING_KS,Missing KS, session not established";
	
	// %s - the ks string, %s - error code, %s - error description
	const INVALID_KS ="INVALID_KS,Invalid KS \"%s\", Error \"%s,%s\"";
	
	//
	const SERVICE_NOT_SPECIFIED = "SERVICE_NOT_SPECIFIED,Service name was not specified, please specify one";
	
	// %s - service name
	const SERVICE_DOES_NOT_EXISTS = "SERVICE_DOES_NOT_EXISTS,Service \"%s\" does not exists";
	
	// %s - xml field  
	const INVALID_PARAMETER_CHAR= "INVALID_PARAMETER_CHAR,Invalid char in \"%s\" field";
	
	//
	const ACTION_NOT_SPECIFIED = "ACTION_NOT_SPECIFIED,Action name was not specified, please specify one";
	
	// %s - action name, %s - service name
	const ACTION_DOES_NOT_EXISTS = "ACTION_DOES_NOT_EXISTS,Action \"%s\" does not exists for service \"%s\"";
	
	// %s - action name
	const ACTION_FORBIDDEN = "ACTION_FORBIDDEN,Action \"%s\" is forbidden for use";
	
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
	
	const OBJECT_TYPE_ABSTRACT = "OBJECT_TYPE_ABSTRACT,The object type \"%s\" is abstract, use one of the object implementations";
	
	const PROPERTY_VALIDATION_CANNOT_BE_NULL =  "PROPERTY_VALIDATION_CANNOT_BE_NULL,The property \"%s\" cannot be NULL";
	
	const PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE = "PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE,Only one of the passed properties: %s should not be null";
	
	const PROPERTY_VALIDATION_MIN_LENGTH = "PROPERTY_VALIDATION_MIN_LENGTH,The property \"%s\" must have a min length of %s characters";
	
	const PROPERTY_VALIDATION_MAX_LENGTH = "PROPERTY_VALIDATION_MAX_LENGTH,The property \"%s\" cannot have more than %s characters";
	
	const PROPERTY_VALIDATION_NUMERIC_VALUE = "PROPERTY_VALIDATION_NUMERIC_VALUE,The property \"%s\" must be numeric";
	
	const PROPERTY_VALIDATION_WRONG_FORMAT = "PROPERTY_VALIDATION_WRONG_FORMAT,The property \"%s\" must match format %s";
	
	const PROPERTY_VALIDATION_MIN_VALUE = "PROPERTY_VALIDATION_MIN_VALUE,The property \"%s\" must have a min value of %s";
	
	const PROPERTY_VALIDATION_MAX_VALUE = "PROPERTY_VALIDATION_MAX_VALUE,The property \"%s\" must have a max value of %s";
	
	const PROPERTY_VALIDATION_NOT_UPDATABLE = "PROPERTY_VALIDATION_NOT_UPDATABLE,The property \"%s\" cannot be updated";
	
	const PROPERTY_VALIDATION_ADMIN_PROPERTY = "PROPERTY_VALIDATION_ADMIN_PROPERTY,The property \"%s\" is updatable with admin session only";
	
	const PROPERTY_VALIDATION_ENTRY_STATUS =  "PROPERTY_VALIDATION_ENTRY_STATUS,The property \"%s\" cannot be set for entry status \"%s\"";
	
	const INVALID_USER_ID = "INVALID_USER_ID,Invalid user id";
	
	const DATA_CENTER_ID_NOT_FOUND = "DATA_CENTER_ID_NOT_FOUND,There is no data center with id [%s]";
	
	const PLUGIN_NOT_AVAILABLE_FOR_PARTNER = "PLUGIN_NOT_AVAILABLE_FOR_PARTNER,Plugin [%s] is not available for partner [%s]";
	
	const SYSTEM_NAME_ALREADY_EXISTS = "SYSTEM_NAME_ALREADY_EXISTS,System name [%s] already exists";
	
	/**
	 * Service Oriented Errors
	 *
	 */
	
	/**
	 * Media Service
	 */
	
	const ENTRY_NOT_READY = "ENTRY_NOT_READY,Entry \"%s\" is not ready";
	
	const INVALID_ENTRY_TYPE = "INVALID_ENTRY_TYPE,Entry \"%s\" type is \"%s\", type must be \"%s\"";
	
	const INVALID_ENTRY_MEDIA_TYPE = "INVALID_ENTRY_MEDIA_TYPE,Entry \"%s\" media type is \"%s\", media type must be \"%s\"";
	
	const ENTRY_ALREADY_WITH_CONTENT = "ENTRY_ALREADY_WITH_CONTENT,Entry already associated with content";
	
	const ENTRY_ID_NOT_REPLACED = "ENTRY_ID_NOT_REPLACED,Entry id \"%s\" not replaced";
	
	const ENTRY_REPLACEMENT_ALREADY_EXISTS = "ENTRY_REPLACEMENT_ALREADY_EXISTS,Entry already in replacement";
	
	const ENTRY_TYPE_NOT_SUPPORTED = "ENTRY_TYPE_NOT_SUPPORTED,Entry type \"%s\" not suppported";
	
	const RESOURCE_TYPE_NOT_SUPPORTED = "RESOURCE_TYPE_NOT_SUPPORTED,Resource type \"%s\" not suppported";
	
	const RESOURCES_MULTIPLE_DATA_CENTERS = "RESOURCES_MULTIPLE_DATA_CENTERS,Resources created on different data centers";
	
	const ENTRY_MEDIA_TYPE_NOT_SUPPORTED = "ENTRY_MEDIA_TYPE_NOT_SUPPORTED,Entry media type \"%s\" not suppported";
	
	const UPLOADED_FILE_NOT_FOUND_BY_TOKEN = "UPLOADED_FILE_NOT_FOUND_BY_TOKEN,The uploaded file was not found by the given token id, or was already used";
	
	const REMOTE_DC_NOT_FOUND = "REMOTE_DC_NOT_FOUND,Remote data center \"%s\" not found";
	
	const LOCAL_FILE_NOT_FOUND = "LOCAL_FILE_NOT_FOUND,Local file was not found \"%s\"";
	
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
	
	const CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID = "CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID,Cannot disable entitlement when widget is not set to an entry";
	
	const CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE = "CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE,Cannot create widget with no entitlement enforcement when current session is with entitlement enabled";
	
	/**
	 * UiConf Service
	 */
	const UICONF_ID_NOT_FOUND = "UICONF_ID_NOT_FOUND,Ui conf id \"%s\" not found";
	
	/**
	 * AccessControl Service
	 */
	const ACCESS_CONTROL_NEW_VERSION_UPDATE = "ACCESS_CONTROL_NEW_VERSION_UPDATE,Access control id \"%s\" should be updated using AccessControlProfile service";
	
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
	
	const FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND = "FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND,Flavor params output id \"%s\" not found";
	
	const THUMB_PARAMS_OUTPUT_ID_NOT_FOUND = "THUMB_PARAMS_OUTPUT_ID_NOT_FOUND,Thumbnail params output id \"%s\" not found";
	
	
	const ASSET_ID_NOT_FOUND = "ASSET_ID_NOT_FOUND,Asset id \"%s\" not found";
	
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
	
	const THUMB_ASSET_IS_DEFAULT = "THUMB_ASSET_IS_DEFAULT,Thumbnail asset \"%s\" is default and could not be deleted";
	
	/**
	 * Category Service
	 */
	const CATEGORY_NOT_FOUND = "CATEGORY_NOT_FOUND,Category id \"%s\" not found";
	
	const CATEGORY_NOT_PERMITTED = "CATEGORY_NOT_PERMITTED,Category \"%s\" is not permitted";
	
	const PARENT_CATEGORY_NOT_FOUND = "PARENT_CATEGORY_NOT_FOUND,Parent category id \"%s\" not found";
	
	const DUPLICATE_CATEGORY = "DUPLICATE_CATEGORY,The category \"%s\" already exists";
	
	const PARENT_CATEGORY_IS_CHILD = "PARENT_CATEGORY_IS_CHILD,The parent category \"%s\" is one of the childs for category \"%s\"";
	
	const CATEGORIES_LOCKED = "CATEGORIES_LOCKED,Categories are locked";
	
	const CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET = "CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET,Cannot inherit members when parent category is not set";
	
	const NOT_ENTITLED_TO_UPDATE_CATEGORY = "NOT_ENTITLED_TO_UPDATE_CATEGORY, Current User is not entitled to update this category";
	
	const CATEGORY_DOES_NOT_HAVE_PARENT_CATEGORY = "CATEGORY_DOES_NOT_HAVE_PARENT_CATEGORY,Category doesn't have parent category";
	
	const CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT = "CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT,Cannot update privacy context";
	
	const CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORY = "CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORIES,Cannot move categories from different parent categories";
	
	const CANNOT_UPDATE_CATEGORY_ENTITLEMENT_FIELDS_WITH_NO_PRIVACY_CONTEXT = "CANNOT_UPDATE_CATEGORY_ENTITLEMENT_FIELDS_WITH_NO_PRIVACY_CONTEXT,Cannot update category's entitlement fields when privacy context is not set on the categroy";
	
	const CANNOT_SET_APPEAR_IN_LIST_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_APPEAR_IN_LIST_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set appear in list field when privacy context is not set on the categroy";
	
	const CANNOT_SET_MODERATION_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_MODERATION_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set moderation field when privacy context is not set on the categroy";
	
	const CANNOT_SET_INHERITANCE_TYPE_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_INHERITANCE_TYPE_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set inheritance field when privacy context is not set on the categroy";
	
	const CANNOT_SET_PRIVACY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_PRIVACY_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set privacy field when privacy context is not set on the categroy";
	
	const CANNOT_SET_OWNER_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_OWNER_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set owner field when privacy context is not set on the categroy";
	
	const CANNOT_SET_USER_JOIN_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_USER_JOIN_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set user join policy field when privacy context is not set on the categroy";
	
	const CANNOT_SET_CONTIRUBUTION_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_CONTIRUBUTION_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set contribution policy field when privacy context is not set on the categroy";
	
	const CANNOT_SET_DEFAULT_PERMISSION_LEVEL_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_DEFAULT_PERMISSION_LEVEL_FIELD_WITH_NO_PRIVACY_CONTEXT, Cannot set default permission level field when privacy context is not set on the categroy";
	
	const PRIVACY_CONTEXT_INVALID_STRING = "PRIVACY_CONTEXT_INVALID_STRING,Privacy context is invalid \"%s\"";
	
	const CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS, Cannot set owner when category is set to inherit";
	
	const CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS, Cannot set user join policy when category is set to inherit";
	
	const CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS, Cannot set default permission level when category is set to inherit";
	
	/**
	 * Batch Service
	 */
	
	const SCHEDULER_HOST_CONFLICT = "SCHEDULER_HOST_CONFLICT, Scheduler id \"%s\" conflicts between hosts: \"%s\" and \"%s\"";
	
	const SCHEDULER_NOT_FOUND = "SCHEDULER_NOT_FOUND, Scheduler id \"%s\" not found";
	
	const WORKER_NOT_FOUND = "WORKER_NOT_FOUND, Worker id \"%s\" not found";
	
	const COMMAND_NOT_FOUND = "COMMAND_NOT_FOUND, Command id \"%s\" not found";
	
	const COMMAND_ALREADY_PENDING = "COMMAND_ALREADY_PENDING, Command already pending";
	
	const PARTNER_NOT_SET = "PARTNER_NOT_SET, Partner not set";
	
	const PARTNER_NOT_FOUND = "PARTNER_NOT_FOUND, Partner not found %s";
	
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
	
	const NON_PARENT_PARTNER_ATTEMPTING_TO_COPY_PARTNER = "NON_PARENT_PARTNER_ATTEMPTING_TO_COPY_PARTNER,Partner id [%s] is not the parent of template partner [%s]";
	
	const INVALID_OBJECT_ID = "INVALID_OBJECT_ID,Invalid object id [%s]";
	
	const USER_NOT_FOUND = "USER_NOT_FOUND,User was not found";
		
	const USER_LOGIN_ALREADY_ENABLED = 'USER_LOGIN_ALREADY_ENABLED,User is already allowed to login';
	
	const USER_LOGIN_ALREADY_DISABLED = 'USER_LOGIN_ALREADY_DISABLED,User is already not allowed to login';
	
	const PROPERTY_VALIDATION_NO_USAGE_PERMISSION = "PROPERTY_VALIDATION_NO_USAGE_PERMISSION,Current user does not have permission to use property \"%s\"";
	
	const PROPERTY_VALIDATION_NO_UPDATE_PERMISSION = "PROPERTY_VALIDATION_NO_UPDATE_PERMISSION,Current user does not have permission to update property \"%s\"";
	
	const PROPERTY_VALIDATION_NO_INSERT_PERMISSION = "PROPERTY_VALIDATION_NO_INSERT_PERMISSION,Current user does not have permission to insert property \"%s\"";
	
	const USER_NOT_ADMIN = "USER_NOT_ADMIN,User [%s] is not admin";
	
	const ROLE_NAME_ALREADY_EXISTS = "ROLE_NAME_ALREADY_EXISTS,A role with the same name already exists";
	
	const PERMISSION_ITEM_NOT_FOUND = "PERMISSION_ITEM_NOT_FOUND,Permission item does not exist";
	
	const PROPERTY_DEPRECATED = "PROPERTY_DEPRECTAED,The property \"%s\" is deprecated and should not be used";
	
	/*
	 * syndication service
	 */
	const INVALID_XSLT = "INVALID_XSLT,Invalid xslt";
	
	const INVALID_XSLT_MISSING_TEMPLATE_RSS = "INVALID_XSLT_MISSING_TEMPLATE_RSS,Invalid xslt, missing rss's template tag with the following convention: xsl:template name=\"rssx\" match=\"/\"";
	
	const INVALID_XSLT_MISSING_TEMPLATE_ITEM = "INVALID_XSLT_MISSING_TEMPLATE_ITEM,Invalid xslt, missing item's template tag with the following convention: xsl:template name=\"item\" match=\"item\"";
	
	const INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM = "INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM,Invalid xslt, missing apply-template tag for item's template with the following convention: xsl:apply-templates name=\"item\"";
	
	const SYNDICATION_FEED_INVALID_STORAGE_ID = "SYNDICATION_FEED_INVALID_STORAGE_ID,Invalid storage id";
	
	const SYNDICATION_FEED_KALTURA_DC_ONLY = "SYNDICATION_FEED_KALTURA_DC_ONLY,Partner configured to use Kaltura data centers only";
	
	/*
	 * file sync
	 */
	const FILE_DOESNT_EXIST = "FILE_DOESNT_EXIST,File doesnt exist";
	
	const FILE_NOT_FOUND = "FILE_NOT_FOUND,File not found";
	
	const STORAGE_PROFILE_ID_NOT_FOUND = "STORAGE_PROFILE_ID_NOT_FOUND,Storage profile id %s not found";
	
	/*
	 * resetUserPassword
	 */
	const CANNOT_RESET_PASSWORD_FOR_SYSTEM_PARTNER = "CANNOT_RESET_PASSWORD_FOR_SYSTEM_PARTNER,Password cannot be reset for system partner";
	
	/*
	 * Report service
	 */
	const REPORT_NOT_FOUND = "REPORT_NOT_FOUND,Report id \"%s\" not found";
	
	const REPORT_NOT_PUBLIC = "REPORT_NOT_PUBLIC,Report id \"%s\" is not public";
	
	const REPORT_PARAMETER_MISSING = "REPORT_PARAMETER_MISSING,Parameter \"%s\" is missing";
	
	const SEARCH_TOO_GENERAL = "SEARCH_TOO_GENERAL,Unable to create report. Query produced too many results";
	
	/**
	 * categoryUser service
	 */
	const INVALID_CATEGORY_USER_ID = "INVALID_CATEGORY_USER_ID,Invalid CategoryUser for category [\"%s\"] and for user [\"%s\"]";
	
	const CATEGORY_USER_ALREADY_EXISTS = "CATEGORY_USER_ALREADY_EXISTS,CategoryUser already exists";
	
	const CATEGORY_INHERIT_MEMBERS = "CATEGORY_INHERIT_MEMBERS,Cannot add members to this category since its inherit members from parent category [\"%s\"]";
	
	const CATEGORY_INHERIT_MEMBERS_MUST_SET_PARENT_CATEGORY = "CATEGORY_INHERIT_MEMBERS_MUST_SET_PARENT_CATEGORY,Category that inherit members must have parent category set";
	
	const CATEGORY_USER_JOIN_NOT_ALLOWED = "CATEGORY_USER_JOIN_NOT_ALLOWED,cannot register to this category [\"%s\"]";
	
	const CANNOT_UPDATE_CATEGORY_USER = "CANNOT_UPDATE_CATEGORY_USER,cannot update categoryUser";
	
	const MUST_FILTER_USERS_OR_CATEGORY = "MUST_FILTER_USERS_OR_CATEGORY, Must filter users or categories";
	
	const CANNOT_OVERRIDE_MANUAL_CHANGES = "CANNOT_OVERRIDE_MANUAL_CHANGES,Cannot override manual changes";
	
	const CANNOT_UPDATE_CATEGORY_USER_OWNER = "CANNOT_UPDATE_CATEGORY_USER_OWNER, Cannot change CategoryUser object for category Owner";
	
	/**
	 * entry
	 */
	
	const ENTRY_CATEGORY_FIELD_IS_DEPRECATED = "ENTRY_CATEGORY_FIELD_IS_DEPRECATED, entry->categories and entry->categoriesIds fields are deprecated - user categoryEntry service";
	
	/**
	 * categoryEntry
	 */
	const INVALID_ENTRY_ID ="INVALID_ENTRY_ID,Invalid entry id [\"%s\"]";
	
	const CANNOT_ASSIGN_ENTRY_TO_CATEGORY = "CANNOT_ASSIGN_ENTRY_TO_CATEGORY,Cannot assign entry to category";

	const CANNOT_REMOVE_ENTRY_FROM_CATEGORY = "CANNOT_REMOVE_ENTRY_FROM_CATEGORY,Cannot remove entry from category";
	
	const CANNOT_ACTIVATE_CATEGORY_ENTRY = "CANNOT_ACTIVATE_CATEGORY_ENTRY,Cannot activate categoryEntry";
	
	const CANNOT_ACTIVATE_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING = "CANNOT_ACTIVATE_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING,Cannot activate a non pending categoryEntry";
	
	const CANNOT_REJECT_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING = "CANNOT_REJECT_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING,Cannot reject a non pending categoryEntry";
	
	const CANNOT_REJECT_CATEGORY_ENTRY = "CANNOT_REJECT_CATEGORY_ENTRY, Cannot reject category entry";
	
	const ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY = "ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY,Entry doesn't assigned to category"; 
	
	const MUST_FILTER_ENTRY_ID_EQUAL = "MUST_FILTER_ENTRY_ID_EQUAL,Must filter on entry id";
	
	const MUST_FILTER_ON_ENTRY_OR_CATEGORY = "MUST_FILTER_ON_ENTRY_OR_CATEGORY,Must filter on entry id or category";
	
	const CATEGORY_ENTRY_ALREADY_EXISTS = "CATEGORY_ENTRY_ALREADY_EXISTS,Entry already assigned to this category";
	
	const CATEGORY_IS_LOCKED = 'CATEGORY_IS_LOCKED,Category is locked - cannot delete or change parent id';
	
	/**
	 * Entitlement
	 */
	const CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE = 'CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE,Cannot index object when enetitlment is enabled'; 
	
	// live stream
	const LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED = 'LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED,Status cannot be determined for live stream protocol [%s]';
	
	const EXTENDING_ITEM_INCOMPATIBLE_COMBINATION = 'EXTENDING_ITEM_INCOMPATIBLE_COMBINATION,This extending object MRSS must replace the XPath contents';
}
