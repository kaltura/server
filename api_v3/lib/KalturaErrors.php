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
	const CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION = "CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION;USER_ID;cannot retrieve another user \"@USER_ID@\" using non-admin session";

	//
	const INTERNAL_SERVERL_ERROR = "INTERNAL_SERVERL_ERROR;;Internal server error occurred";

	// should be used for internal actions only
	const INTERNAL_SERVERL_ERROR_DEBUG = "INTERNAL_SERVERL_ERROR;ERROR;Internal server error occurred \"@ERROR@\"";

	//
	const MISSING_KS = "MISSING_KS;;Missing KS, session not established";

	// KS - the ks string, ERR_CODE - error code, ERR_DESC - error description
	const INVALID_KS = "INVALID_KS;KSID,ERR_CODE,ERR_DESC;Invalid KS \"@KSID@\", Error \"@ERR_CODE@,@ERR_DESC@\"";

	//
	const SERVICE_NOT_SPECIFIED = "SERVICE_NOT_SPECIFIED;;Service name was not specified, please specify one";

	// SRV_NAME - service name
	const SERVICE_DOES_NOT_EXISTS = "SERVICE_DOES_NOT_EXISTS;SRV_NAME;Service \"@SRV_NAME@\" does not exists";

	// XML_FIELD - xml field
	const INVALID_PARAMETER_CHAR= "INVALID_PARAMETER_CHAR;XML_FIELD;Invalid char in \"@XML_FIELD@\" field";

	//
	const ACTION_NOT_SPECIFIED = "ACTION_NOT_SPECIFIED;;Action name was not specified, please specify one";

	// ACTION_NAME - action name, SERVICE_NAME - service name
	const ACTION_BLOCKED = "ACTION_BLOCKED;ACTION_NAME,SERVICE_NAME;Action \"@ACTION_NAME@\" in service \"@SERVICE_NAME@\" is blocked";

	// ACTION_NAME - action name, SERVICE_NAME - service name
	const ACTION_DOES_NOT_EXISTS = "ACTION_DOES_NOT_EXISTS;ACTION_NAME,SERVICE_NAME;Action \"@ACTION_NAME@\" does not exists for service \"@SERVICE_NAME@\"";

	// ACTION_NAME - action name
	const ACTION_FORBIDDEN = "ACTION_FORBIDDEN;ACTION_NAME;Action \"@ACTION_NAME@\" is forbidden for use";

	// PARAM_NAME - parameter name
	const MISSING_MANDATORY_PARAMETER = "MISSING_MANDATORY_PARAMETER;PARAM_NAME;Missing parameter \"@PARAM_NAME@\"";

	// INVALID_OBJ_TYPE - invalid object type
	const INVALID_OBJECT_TYPE = "INVALID_OBJECT_TYPE;INVALID_OBJ_TYPE;Invalid object type \"@INVALID_OBJ_TYPE@\"";

	// ENUM_VAL - enum value, PARAM_NAME - parameter name, ENUM_TYPE - enum type
	const INVALID_ENUM_VALUE = "INVALID_ENUM_VALUE;ENUM_VAL,PARAM_NAME,ENUM_TYPE;Invalid enumeration value \"@ENUM_VAL@\" for parameter \"@PARAM_NAME@\", expecting enumeration type \"@ENUM_TYPE@\"";

	// PID - partner id
	const INVALID_PARTNER_ID = "INVALID_PARTNER_ID;PID;Invalid partner id \"@PID@\"";

	const DIRECT_LOGIN_BLOCKED = "DIRECT_LOGIN_BLOCKED;;Direct login is blocked on this partner";

	// SRV_NAME - service , ACTION_NAME - action
	const INVALID_SERVICE_CONFIGURATION = "INVALID_SERVICE_CONFIGURATION;SRV_NAME,ACTION_NAME;Invalid service configuration. Unknown service [@SRV_NAME@:@ACTION_NAME@].";

	const OBJECT_TYPE_ABSTRACT = "OBJECT_TYPE_ABSTRACT;OBJ_TYPE;The object type \"@OBJ_TYPE@\" is abstract, use one of the object implementations";

	const PROPERTY_VALIDATION_CANNOT_BE_NULL =  "PROPERTY_VALIDATION_CANNOT_BE_NULL;PROP_NAME;The property \"@PROP_NAME@\" cannot be null";

	const PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE = "PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE;PROP_NAME;Only one of the passed properties: @PROP_NAME@ should not be null";

	const PROPERTY_VALIDATION_MIN_LENGTH = "PROPERTY_VALIDATION_MIN_LENGTH;PROP_NAME,MIN_LEN;The property \"@PROP_NAME@\" must have a min length of @MIN_LEN@ characters";

	const PROPERTY_VALIDATION_MAX_LENGTH = "PROPERTY_VALIDATION_MAX_LENGTH;PROP_NAME,MAX_LEN;The property \"@PROP_NAME@\" cannot have more than @MAX_LEN@ characters";

	const PROPERTY_VALIDATION_NUMERIC_VALUE = "PROPERTY_VALIDATION_NUMERIC_VALUE;PROP_NAME;The property \"@PROP_NAME@\" must be numeric";

	const PROPERTY_VALIDATION_WRONG_FORMAT = "PROPERTY_VALIDATION_WRONG_FORMAT;PROP_NAME,FORMAT;The property \"@PROP_NAME@\" must match format @FORMAT@";

	const PROPERTY_VALIDATION_MIN_VALUE = "PROPERTY_VALIDATION_MIN_VALUE;PROP_NAME,MIN_VAL;The property \"@PROP_NAME@\" must have a min value of @MIN_VAL@";

	const PROPERTY_VALIDATION_MAX_VALUE = "PROPERTY_VALIDATION_MAX_VALUE;PROP_NAME,MAX_VAL;The property \"@PROP_NAME@\" must have a max value of @MAX_VAL@";

	const PROPERTY_VALIDATION_NOT_UPDATABLE = "PROPERTY_VALIDATION_NOT_UPDATABLE;PROP_NAME;The property \"@PROP_NAME@\" cannot be updated";

	const PROPERTY_VALIDATION_ADMIN_PROPERTY = "PROPERTY_VALIDATION_ADMIN_PROPERTY;PROP_NAME;The property \"@PROP_NAME@\" is updatable with admin session only";

	const PROPERTY_VALIDATION_ENTRY_STATUS =  "PROPERTY_VALIDATION_ENTRY_STATUS;PROP_NAME,STATUS;The property \"@PROP_NAME@\" cannot be set for entry status \"@STATUS@\"";

	const INVALID_USER_ID = "INVALID_USER_ID;;Invalid user id";

	const INVALID_METADATA_PROFILE_ID = "INVALID_USER_ID;;Invalid user id";

	const DATA_CENTER_ID_NOT_FOUND = "DATA_CENTER_ID_NOT_FOUND;DCID;There is no data center with id [@DCID@]";

	const PLUGIN_NOT_AVAILABLE_FOR_PARTNER = "PLUGIN_NOT_AVAILABLE_FOR_PARTNER;PLUGIN,PARTNER;Plugin [@PLUGIN@] is not available for partner [@PARTNER@]";

	const SYSTEM_NAME_ALREADY_EXISTS = "SYSTEM_NAME_ALREADY_EXISTS;SYS_NAME;System name [@SYS_NAME@] already exists";

	const SCHEDULE_EVENT_RESOURCE_ALREADY_EXISTS= "SCHEDULE_EVENT_RESOURCE_ALREADY_EXISTS;EVENT_ID,RESOURCE_ID;Schedule event resource already exists with eventId[@EVENT_ID@] and resourceId (@RESOURCE_ID@)";

	const RESOURCE_PARENT_ID_NOT_FOUND = "RESOURCE_PARENT_ID_NOT_FOUND;PARENT_ID;Resource parent id [@PARENT_ID@] not found";

	const SCHEDULE_RESOURCE_ID_NOT_FOUND = "SCHEDULE_RESOURCE_ID_NOT_FOUND;ID;Schedule Resource id [@ID@] not found";

	const SCHEDULE_EVENT_ID_NOT_FOUND = "SCHEDULE_EVENT_ID_NOT_FOUND;ID;Schedule Event id [@ID@] not found";

	const LOCK_TIMED_OUT = "LOCK_TIMED_OUT;;Timed out while attempting to grab lock";

	const MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED = "MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED;OBJECT_ID;Max update limit was reached. Object ID \"@OBJECT_ID@\" will not updated with latest chnages";

	const RESOURCE_IS_RESERVED = "RESOURCE_IS_RESERVED;RESOURCE_ID;Resource with id @RESOURCE_ID@ is already reserved";

	const FAILED_TO_CALCULATE_DYNAMIC_DEPENDENT_VALUE = "FAILED_TO_CALCULATE_DYNAMIC_DEPENDENT_VALUE;DYNAMIC_VALUE;Dynamic dependent value \"@DYNAMIC_VALUE@\" not found";

	const FAILED_TO_INIT_OBJECT = "FAILED_TO_INIT_OBJECT;RESOURCE_ID;Failed to initialize necessary object";
	
	const ANONYMOUS_ACCESS_FORBIDDEN = "ANONYMOUS_ACCESS_FORBIDDEN;;Anonymous access to this functionality is forbidden";
	/**
	 * Service Oriented Errors
	 *
	 */

	/**
	 * Media Service
	 */

	const ENTRY_NOT_READY = "ENTRY_NOT_READY;ENTRY_NAME;Entry \"@ENTRY_NAME@\" is not ready";

	const INVALID_ENTRY_TYPE = "INVALID_ENTRY_TYPE;ENTRY_NAME,WRONG_ENTRY_TYPE,RIGHT_ENTRY_TYPE;Entry \"@ENTRY_NAME@\" type is \"@WRONG_ENTRY_TYPE@\", type must be \"@RIGHT_ENTRY_TYPE@\"";

	const INVALID_ENTRY_MEDIA_TYPE = "INVALID_ENTRY_MEDIA_TYPE;ENTRY_NAME,WRONG_MEDIA_TYPE,RIGHT_MEDIA_TYPE;Entry \"@ENTRY_NAME@\" media type is \"@WRONG_MEDIA_TYPE@\", media type must be \"@RIGHT_MEDIA_TYPE@\"";

	const ENTRY_ALREADY_WITH_CONTENT = "ENTRY_ALREADY_WITH_CONTENT;;Entry already associated with content";

	const ENTRY_ID_NOT_REPLACED = "ENTRY_ID_NOT_REPLACED;ENTRY_ID;Entry id \"@ENTRY_ID@\" not replaced";

	const ENTRY_REPLACEMENT_ALREADY_EXISTS = "ENTRY_REPLACEMENT_ALREADY_EXISTS;;Entry already in replacement";

	const ENTRY_TYPE_NOT_SUPPORTED = "ENTRY_TYPE_NOT_SUPPORTED;ENTRY_TYPE;Entry type \"@ENTRY_TYPE@\" not supported";

	const RESOURCE_TYPE_NOT_SUPPORTED = "RESOURCE_TYPE_NOT_SUPPORTED;RES_TYPE;Resource type \"@RES_TYPE@\" not supported";

	const RESOURCES_MULTIPLE_DATA_CENTERS = "RESOURCES_MULTIPLE_DATA_CENTERS;;Resources created on different data centers";

	const ENTRY_MEDIA_TYPE_NOT_SUPPORTED = "ENTRY_MEDIA_TYPE_NOT_SUPPORTED;MEDIA_TYPE;Entry media type \"@MEDIA_TYPE@\" not supported";

	const ENTRY_SOURCE_TYPE_NOT_SUPPORTED = "ENTRY_SOURCE_TYPE_NOT_SUPPORTED;SOURCE_TYPE;Entry source type \"@SOURCE_TYPE@\" not supported";

	const UPLOADED_FILE_NOT_FOUND_BY_TOKEN = "UPLOADED_FILE_NOT_FOUND_BY_TOKEN;;The uploaded file was not found by the given token id, or was already used";

	const REMOTE_DC_NOT_FOUND = "REMOTE_DC_NOT_FOUND;DC;Remote data center \"@DC@\" not found";

	const LOCAL_FILE_NOT_FOUND = "LOCAL_FILE_NOT_FOUND;FILE;Local file was not found \"@FILE@\"";

	const RECORDED_WEBCAM_FILE_NOT_FOUND = "RECORDED_WEBCAM_FILE_NOT_FOUND;;The recorded webcam file was not found by the given token id, or was already used";

	const INVALID_WEBCAM_TOKEN_ID = "INVALID_WEBCAM_TOKEN_ID;;Invalid webcam token id";

	const PERMISSION_DENIED_TO_UPDATE_ENTRY = "PERMISSION_DENIED_TO_UPDATE_ENTRY;;User can update only the entries he own, otherwise an admin session must be used";

	const INVALID_RANK_VALUE = "INVALID_RANK_VALUE;;Invalid rank value, rank should be between 1 and 5";

	const MAX_CATEGORIES_FOR_ENTRY_REACHED = "MAX_CATEGORIES_FOR_ENTRY_REACHED;CATEGORIES;Entry can be linked with a maximum of \"@CATEGORIES@\" categories";

	const MAX_ASSETS_FOR_ENTRY_REACHED = "MAX_ASSETS_FOR_ENTRY_REACHED;ASSETS;Entry can contain maximum of \"@ASSETS@\" assets";

	const INVALID_ENTRY_SCHEDULE_DATES = "INVALID_ENTRY_SCHEDULE_DATES;;Invalid entry schedule dates";

	const INVALID_ENTRY_STATUS = "INVALID_ENTRY_STATUS;;Invalid entry status";

	const ENTRY_CANNOT_BE_FLAGGED = "ENTRY_CANNOT_BE_FLAGGED;;Entry cannot be flagged";

	const ENTRY_CANNOT_BE_TRIMMED = "ENTRY_CANNOT_BE_TRIMMED;;Entry cannot be trimmed";

	const DYNAMIC_SEGMENT_DURATION_DISABLED = "DYNAMIC_SEGMENT_DURATION_DISABLED;;Cannot edit segment duration. Dynamic segment duration feature is disabled";

	const VOLUME_MAP_NOT_CONFIGURED = "VOLUME_MAP_NOT_CONFIGURED;;Need to add volume map support to configuration";

	const RESOURCE_ENTRY_ID_MISSING= "RESOURCE_ENTRY_ID_MISSING;;Entry Id on resource object is missing";
	
	const CYCLE_IN_PARENTAGE = "CYCLE_IN_PARENTAGE;;Invalid cycle detected in the parent child connection of this entry";


	/**
	 * Notification Service
	 */

	const NOTIFICATION_FOR_ENTRY_NOT_FOUND = "NOTIFICATION_FOR_ENTRY_NOT_FOUND;ENTRY;Notification for entry id \"@ENTRY@\" not found";

	/**
	 * Bulk Upload Service
	 */

	const BULK_UPLOAD_NOT_FOUND = "BULK_UPLOAD_NOT_FOUND;ID;Bulk upload id \"@ID@\" not found";

	/**
	 * Pexip Service
	 */

	const PEXIP_MAP_NOT_CONFIGURED = "PEXIP_MAP_NOT_CONFIGURED;;Need to add pexip map support to configuration";

	const MISSING_SIP_CONFIGURATIONS = "MISSING_SIP_CONFIGURATIONS;;Missing sip configurations";

	const PEXIP_FAILED_TO_GENERATE_TOKEN = "PEXIP_FAILED_TO_GENERATE_TOKEN;;Failed to generate ecrypted sip token. no more retries";

	const INVALID_SIP_SOURCE_TYPE = "INVALID_SIP_SOURCE_TYPE;;Invalid Sip source type";

	const PEXIP_ROOM_CREATION_FAILED = "PEXIP_ROOM_CREATION_FAILED;ENTRY;Can't create virtual room for entry \"@ENTRY@\" ";

	const PEXIP_ADP_CREATION_FAILED = "PEXIP_ADP_CREATION_FAILED;ENTRY;Can't create virtual ADP for entry \"@ENTRY@\" ";

	const SIP_ENTRY_SERVER_NODE_CREATION_FAILED = "SIP_ENTRY_SERVER_NODE_CREATION_FAILED ;ENTRY;Can't create sip entry server node for entry \"@ENTRY@\" ";



	/**
	 * Widget Service
	 */

	const SOURCE_WIDGET_OR_UICONF_REQUIRED = "SOURCE_WIDGET_OR_UICONF_REQUIRED;;SourceWidgetId or UiConfId id are required";

	const SOURCE_WIDGET_NOT_FOUND = "SOURCE_WIDGET_NOT_FOUND;ID;Source widget id \"@ID@\" not found";

	const CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID = "CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID;;Cannot disable entitlement when widget is not set to an entry";

	const CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE = "CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE;;Cannot create widget with no entitlement enforcement when current session is with entitlement enabled";

	/**
	 * UiConf Service
	 */
	const UICONF_ID_NOT_FOUND = "UICONF_ID_NOT_FOUND;ID;UI conf id \"@ID@\" not found";

	/**
	 * AccessControl Service
	 */
	const ACCESS_CONTROL_NEW_VERSION_UPDATE = "ACCESS_CONTROL_NEW_VERSION_UPDATE;ID;Access control id \"@ID@\" should be updated using AccessControlProfile service";

	const ACCESS_CONTROL_ID_NOT_FOUND = "ACCESS_CONTROL_ID_NOT_FOUND;ID;Access control id \"@ID@\" not found";

	const MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED = "MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED;MAX_NUM;Max number of \"@MAX_NUM@\" access controls was reached";

	const CANNOT_DELETE_DEFAULT_ACCESS_CONTROL = "CANNOT_DELETE_DEFAULT_ACCESS_CONTROL;;Default access control cannot be deleted";

	const EXCEEDED_ENTRIES_PER_ACCESS_CONTROL_FOR_UPDATE = "EXCEEDED_ENTRIES_PER_ACCESS_CONTROL_FOR_UPDATE;id;exceeded entries per access control id @id@ for update";

	const CANNOT_TRANSFER_ENTRIES_TO_ANOTHER_ACCESS_CONTROL_OBJECT = "CANNOT_TRANSFER_ENTRIES_TO_ANOTHER_ACCESS_CONTROL_OBJECT;;no default access control for current partner";

	/**
	 * App Token
	 */
	const APP_TOKEN_ID_NOT_FOUND = "APP_TOKEN_ID_NOT_FOUND;ID;Application token id \"@ID@\" not found";

	const APP_TOKEN_NOT_ACTIVE = "APP_TOKEN_NOT_ACTIVE;ID;Application token id \"@ID@\" not active";

	const APP_TOKEN_EXPIRED = "APP_TOKEN_EXPIRED;ID;Application token id \"@ID@\" expired";

	const INVALID_APP_TOKEN_HASH = "INVALID_APP_TOKEN_HASH;;Invalid application token hash";


	/**
	 * ConversionProfile Service
	 */
	const CONVERSION_PROFILE_ID_NOT_FOUND = "CONVERSION_PROFILE_ID_NOT_FOUND;ID;Conversion profile id \"@ID@\" not found";

	const INGESTION_PROFILE_ID_NOT_FOUND = "INGESTION_PROFILE_ID_NOT_FOUND;ID;Ingestion profile id \"@ID@\" not found";

	const CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE = "CANNOT_DELETE_DEFAULT_CONVERSION_PROFILE;;Default conversion profile cannot be deleted";

	const ASSET_PARAMS_INVALID_TYPE = "ASSET_PARAMS_INVALID_TYPE;ASSET_PARAMS_ID,TYPE;Asset params id \"@ASSET_PARAMS_ID@\" type \"@TYPE@\" is invalid";

	const CONVERSION_PROFILE_ASSET_PARAMS_NOT_FOUND = "CONVERSION_PROFILE_ASSET_PARAMS_NOT_FOUND;PROFILE_ID,PARAMS_ID;Conversion profile id \"@PROFILE_ID@\" asset params id \"@PARAMS_ID@\" not found";

	const INGEST_NOT_FOUND_IN_CONVERSION_PROFILE = "INGEST_NOT_FOUND_IN_CONVERSION_PROFILE;STREAM_NAME;Ingest \"@STREAM_NAME@\" is not in conversion profile";

	const SOURCE_FLAVOR_CHANGED_DURING_CONVERSION = "SOURCE_FLAVOR_CHANGED_DURING_CONVERSION;CURRENT_SOURCE_FILE,ORIGINAL_SOURCE_FILE,ID;Source flavor was changed during conversion, current - \"@CURRENT_SOURCE_FILE@\" ,original source file - \"@ORIGINAL_SOURCE_FILE@\", ID - \"@ID@\" ,  aborting.";

	/**
	 * FlavorParams Service
	 */
	const FLAVOR_PARAMS_ID_NOT_FOUND = "FLAVOR_PARAMS_ID_NOT_FOUND;ID;Flavor params id \"@ID@\" not found";

	const FLAVOR_PARAMS_NOT_FOUND = "FLAVOR_PARAMS_NOT_FOUND;;Flavor params not found";

	const FLAVOR_PARAMS_DUPLICATE = "FLAVOR_PARAMS_DUPLICATE;PARAMS;Flavor params [@PARAMS@] defined more than once";

	const FLAVOR_PARAMS_SOURCE_DUPLICATE = "FLAVOR_PARAMS_SOURCE_DUPLICATE;;More than onc source flavor defined";

	const FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND = "FLAVOR_PARAMS_OUTPUT_ID_NOT_FOUND;ID;Flavor params output id \"@ID@\" not found";

	const THUMB_PARAMS_OUTPUT_ID_NOT_FOUND = "THUMB_PARAMS_OUTPUT_ID_NOT_FOUND;ID;Thumbnail params output id \"@ID@\" not found";

	const ASSET_ID_NOT_FOUND = "ASSET_ID_NOT_FOUND;ID;Asset id \"@ID@\" not found";

	const ASSET_PARAMS_ORIGIN_NOT_SUPPORTED = "LIVE_PARAMS_ORIGIN_NOT_SUPPORTED;ID,TYPE,ORIGIN;Asset params @ID@ of type @TYPE@ does not support origin @ORIGIN@";

	/**
	 * FlavorAsset Service
	 */
	const ASSET_NOT_ALLOWED = "ASSET_NOT_ALLOWED;ID;Flavor asset id \"@ID@\" not allowed";

	const FLAVOR_ASSET_ID_NOT_FOUND = "FLAVOR_ASSET_ID_NOT_FOUND;ID;Flavor asset id \"@ID@\" not found";

	const FLAVOR_ASSET_ALREADY_EXISTS = "FLAVOR_ASSET_ALREADY_EXISTS;ASSET_ID,PARAMS_ID;Flavor asset id \"@ASSET_ID@\" already use flavor params id \"@PARAMS_ID@\"";

	const FLAVOR_ASSET_RECONVERT_ORIGINAL = "FLAVOR_ASSET_RECONVERT_ORIGINAL;;Cannot reconvert original flavor asset";

	const ORIGINAL_FLAVOR_ASSET_IS_MISSING = "ORIGINAL_FLAVOR_ASSET_IS_MISSING;;The original flavor asset is missing";


	const ORIGINAL_FLAVOR_ASSET_NOT_CREATED = "ORIGINAL_FLAVOR_ASSET_NOT_CREATED;DATA;The original flavor asset could not be created [@DATA@]";

	const NO_FLAVORS_FOUND = "NO_FLAVORS_FOUND;;No flavors found";

	const NO_EXTERNAL_CONTENT_EXISTS = "NO_EXTERNAL_CONTENT_EXISTS;;Can't delete local content because no external content exists";

	const GENERATE_TRANSCODING_COMMAND_FAIL = "GENERATE_TRANSCODING_COMMAND_FAIL;ASSET_ID,MEDIA_INFO,REASON;Failed to create proper transcoding command for asset id \"@ASSET_ID@\" and ffprobe info [@MEDIA_INFO] due to @REASON@";

	/**
	 * ThumbAsset Service
	 */
	const THUMB_ASSET_ID_NOT_FOUND = "THUMB_ASSET_ID_NOT_FOUND;ID;The Thumbnail asset id \"@ID@\" not found";

	const THUMB_ASSET_PARAMS_ID_NOT_FOUND = "THUMB_ASSET_ID_NOT_FOUND;ID;The Thumbnail asset not found for params id \"@ID@\"";

	const THUMB_ASSET_IS_NOT_READY = "THUMB_ASSET_IS_NOT_READY;;The thumbnail asset is not ready";

	const THUMB_ASSET_ALREADY_EXISTS = "THUMB_ASSET_ALREADY_EXISTS;ASSET_ID,PARAMS_ID;Thumbnail asset id \"@ASSET_ID@\" already use thumbnail params id \"@PARAMS_ID@\"";

	const THUMB_ASSET_DOWNLOAD_FAILED = "THUMB_ASSET_DOWNLOAD_FAILED;URL_PATH;Failed to download thumbnail from URL \"@URL_PATH@\"";

	const THUMB_ASSET_IS_DEFAULT = "THUMB_ASSET_IS_DEFAULT;ASSET;Thumbnail asset \"@ASSET@\" is default and could not be deleted";

	const THUMB_ASSET_ID_IS_NOT_TIMED_THUMB_TYPE = "THUMB_ASSET_IS_NOT_OF_TYPE_TIMED_THUMB;ASSET;Thumbnail asset \"@ASSET@\" is not of type timed thumb";

	const FILE_CONTENT_NOT_SECURE = "FILE_CONTENT_NOT_SECURE;;File content contains potential security risks";

	/**
	 * Category Service
	 */
	const CATEGORY_NOT_FOUND = "CATEGORY_NOT_FOUND;ID;Category id \"@ID@\" not found";

	const CATEGORY_NOT_PERMITTED = "CATEGORY_NOT_PERMITTED;CATEGORY;Category \"@CATEGORY@\" is not permitted";

	const PARENT_CATEGORY_NOT_FOUND = "PARENT_CATEGORY_NOT_FOUND;ID;Parent category id \"@ID@\" not found";

	const DUPLICATE_CATEGORY = "DUPLICATE_CATEGORY;CATEGORY;The category \"@CATEGORY@\" already exists";

	const PARENT_CATEGORY_IS_CHILD = "PARENT_CATEGORY_IS_CHILD;PARENT_CATEGORY,OTHER_CATEGORY;The parent category \"@PARENT_CATEGORY@\" is one of the children for category \"@OTHER_CATEGORY@\"";

	const CATEGORIES_LOCKED = "CATEGORIES_LOCKED;;Categories are locked";

	const CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET = "CANNOT_INHERIT_MEMBERS_WHEN_PARENT_CATEGORY_IS_NOT_SET;;Cannot inherit members when parent category is not set";

	const NOT_ENTITLED_TO_UPDATE_CATEGORY = "NOT_ENTITLED_TO_UPDATE_CATEGORY;;Current User is not entitled to update this category";

	const CATEGORY_DOES_NOT_HAVE_PARENT_CATEGORY = "CATEGORY_DOES_NOT_HAVE_PARENT_CATEGORY;;Category doesn't have parent category";

	const CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT = "CANNOT_UPDATE_CATEGORY_PRIVACY_CONTEXT;;Cannot update privacy context";

	const CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORY = "CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORIES;;Cannot move categories from different parent categories";

	const CANNOT_UPDATE_CATEGORY_ENTITLEMENT_FIELDS_WITH_NO_PRIVACY_CONTEXT = "CANNOT_UPDATE_CATEGORY_ENTITLEMENT_FIELDS_WITH_NO_PRIVACY_CONTEXT;;Cannot update category's entitlement fields when privacy context is not set on the categroy";

	const CANNOT_SET_APPEAR_IN_LIST_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_APPEAR_IN_LIST_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set appear in list field when privacy context is not set on the categroy";

	const CANNOT_SET_MODERATION_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_MODERATION_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set moderation field when privacy context is not set on the categroy";

	const CANNOT_SET_INHERITANCE_TYPE_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_INHERITANCE_TYPE_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set inheritance field when privacy context is not set on the categroy";

	const CANNOT_SET_PRIVACY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_PRIVACY_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set privacy field when privacy context is not set on the categroy";

	const CANNOT_SET_OWNER_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_OWNER_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set owner field when privacy context is not set on the categroy";

	const CANNOT_SET_USER_JOIN_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_USER_JOIN_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set user join policy field when privacy context is not set on the categroy";

	const CANNOT_SET_OWNER_FIELD_WITH_USER_ID = "CANNOT_SET_OWNER_FIELD_WITH_USER_ID;ID;Cannot set owner field with user id \"@ID@\", user id is invalid";

	const CANNOT_SET_CONTIRUBUTION_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_CONTIRUBUTION_POLICY_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set contribution policy field when privacy context is not set on the categroy";

	const CANNOT_SET_DEFAULT_PERMISSION_LEVEL_FIELD_WITH_NO_PRIVACY_CONTEXT = "CANNOT_SET_DEFAULT_PERMISSION_LEVEL_FIELD_WITH_NO_PRIVACY_CONTEXT;;Cannot set default permission level field when privacy context is not set on the categroy";

	const PRIVACY_CONTEXT_INVALID_STRING = "PRIVACY_CONTEXT_INVALID_STRING;CONTEXT;Privacy context is invalid \"@CONTEXT@\"";

	const CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_OWNER_WHEN_CATEGORY_INHERIT_MEMBERS;;Cannot set owner when category is set to inherit";

	const CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_USER_JOIN_POLICY_WHEN_CATEGORY_INHERIT_MEMBERS;;Cannot set user join policy when category is set to inherit";

	const CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS = "CANNOT_SET_DEFAULT_PERMISSION_LEVEL_WHEN_CATEGORY_INHERIT_MEMBERS;;Cannot set default permission level when category is set to inherit";

	const CANNOT_SET_MULTI_PRIVACY_CONTEXT = "CANNOT_SET_MULTI_PRIVACY_CONTEXT;; Cannot set multiple privacy context when Disable Category Limit feature is turned on";

	const AGGREGATION_CATEGORY_WRONG_ASSOCIATION = "AGGREGATION_CATEGORY_WRONG_ASSOCIATION;; Cannot create aggregation category association";

	const CATEGORY_NAME_CONTAINS_INVALID_CHARS = "CATEGORY_NAME_CONTAINS_INVALID_CHARS;;Category name contains invalid chars.";

	/**
	 * Batch Service
	 */

	const FILE_ALREADY_EXISTS = "FILE_ALREADY_EXISTS;PATH;File already exists \"@PATH@\" ";

	const PATH_NOT_ALLOWED = "PATH_NOT_ALLOWED;PATH;Path not allowed \"@PATH@\" ";

	const FILE_SIZE_EXCEEDED = "FILE_SIZE_EXCEEDED;FILE_SIZE;File size exceeded \"@FILE_SIZE@\" ";

	const SCHEDULER_HOST_CONFLICT = "SCHEDULER_HOST_CONFLICT;SCHED_ID,HOST1,HOST2;Scheduler id \"@SCHED_ID@\" conflicts between hosts: \"@HOST1@\" and \"@HOST2@\"";

	const SCHEDULER_NOT_FOUND = "SCHEDULER_NOT_FOUND;ID;Scheduler id \"@ID@\" not found";

	const MAX_CONFIGURED_ID_NOT_FOUND = "MAX_CONFIGURED_ID_NOT_FOUND;;Could not retrieve max configured_id";

	const WORKER_NOT_FOUND = "WORKER_NOT_FOUND;ID;Worker id \"@ID@\" not found";

	const COMMAND_NOT_FOUND = "COMMAND_NOT_FOUND;ID;Command id \"@ID@\" not found";

	const COMMAND_ALREADY_PENDING = "COMMAND_ALREADY_PENDING;;Command already pending";

	const PARTNER_NOT_SET = "PARTNER_NOT_SET;;Partner not set";

	const PARTNER_NOT_FOUND = "PARTNER_NOT_FOUND;PARTNER;Partner not found @PARTNER@";

	/**
	 * Upload Service
	 */
	const INVALID_UPLOAD_TOKEN_ID = "INVALID_UPLOAD_TOKEN_ID;;Invalid upload token id";

	const UPLOAD_PARTIAL_ERROR = "UPLOAD_PARTIAL_ERROR;;File was uploaded partially";

	const UPLOAD_ERROR = "UPLOAD_ERROR;;Upload failed";

	const UPLOADED_FILE_NOT_FOUND = "UPLOADED_FILE_NOT_FOUND;FILE;Uploaded file not found [@FILE@]";

	const BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR;;Unable to create file sync object for bulk upload result";

	const BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR;;Unable to create file sync object for flavor conversion";

	/**
	 * Upload Token Service
	 */
	const UPLOAD_TOKEN_NOT_FOUND = "UPLOAD_TOKEN_NOT_FOUND;;Upload token not found";

	const UPLOAD_TOKEN_INVALID_STATUS_FOR_UPLOAD = "UPLOAD_TOKEN_INVALID_STATUS_FOR_UPLOAD;;Upload token is in an invalid status for uploading a file, maybe the file was already uploaded";

	const UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY = "UPLOAD_TOKEN_INVALID_STATUS_FOR_ADD_ENTRY;;Upload token is in an invalid status for adding entry, maybe the a file was not uploaded or the token was used";

	const UPLOAD_TOKEN_CANNOT_RESUME = "UPLOAD_TOKEN_CANNOT_RESUME;;Cannot resume the upload, original file was not found";

	const UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE = "UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE;;Failed to match expected file size";

	const UPLOAD_TOKEN_FILE_TYPE_RESTRICTED_FOR_UPLOAD = "UPLOAD_TOKEN_FILE_TYPE_RESTRICTED_FOR_UPLOAD;;Upload token is restricted due to the file type";

	const UPLOAD_TOKEN_MISSING_FILE_SIZE = "UPLOAD_TOKEN_MISSING_FILE_SIZE;;FileSize is mandatory when enabling autoFinalize";
	
	const MAX_ALLOWED_CHUNK_COUNT_EXCEEDED = "MAX_ALLOWED_CHUNK_COUNT_EXCEEDED;;Max allowed waiting chunks to be concatenated has exceeded allowed limit";
	
	const UPLOAD_PASSED_MAX_RESUME_TIME_ALLOWED = "UPLOAD_PASSED_MAX_RESUME_TIME_ALLOWED;MAX_RESUME_TIME;Max resume time of @MAX_RESUME_TIME@ seconds reached, cannot resume upload";

	/*
	 * Partenrs service
	 * PID - the parent partner_id
	 */
	const NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD = "NON_GROUP_PARTNER_ATTEMPTING_TO_ASSIGN_CHILD;PID;Partner id [@PID@] is not a VAR/GROUP, but is attempting to create child partner";

	const NON_PARENT_PARTNER_ATTEMPTING_TO_COPY_PARTNER = "NON_PARENT_PARTNER_ATTEMPTING_TO_COPY_PARTNER;PID,T_PARTNER;Partner id [@PID@] is not the parent of template partner [@T_PARTNER@]";

	const INVALID_OBJECT_ID = "INVALID_OBJECT_ID;ID;Invalid object id [@ID@]";

	const FAILED_TO_CREATE_BULK_DELETE = "FAILED_TO_CREATE_BULK_DELETE;;Failed to create bulk delete" ;

	const USER_NOT_FOUND = "USER_NOT_FOUND;;User was not found";

	const GROUP_NOT_FOUND = "GROUP_NOT_FOUND;;Group was not found";

	const GROUP_USER_NOT_FOUND = "GROUP_USER_NOT_FOUND;;Group user was not found";

	const GROUP_USER_ALREADY_EXISTS = "GROUP_USER_ALREADY_EXISTS;;GroupUser already exists";

	const USER_EXCEEDED_MAX_GROUPS = "USER_EXCEEDED_MAX_GROUPS;;User exceeded max number of groups";

	const GROUP_USER_DOES_NOT_EXIST = "GROUP_USER_DOES_NOT_EXISTS;USER,GROUP;Invalid GroupUser for group [\"@GROUP@\"] and for user [\"@USER@\"]";

	const USER_LOGIN_ALREADY_ENABLED = "USER_LOGIN_ALREADY_ENABLED;;User is already allowed to login";

	const USER_LOGIN_ALREADY_DISABLED = "USER_LOGIN_ALREADY_DISABLED;;User is already not allowed to login";

	const PROPERTY_VALIDATION_NO_USAGE_PERMISSION = "PROPERTY_VALIDATION_NO_USAGE_PERMISSION;PROP;Current user does not have permission to use property \"@PROP@\"";

	const PROPERTY_VALIDATION_NO_UPDATE_PERMISSION = "PROPERTY_VALIDATION_NO_UPDATE_PERMISSION;PROP;Current user does not have permission to update property \"@PROP@\"";

	const PROPERTY_VALIDATION_NO_INSERT_PERMISSION = "PROPERTY_VALIDATION_NO_INSERT_PERMISSION;PROP;Current user does not have permission to insert property \"@PROP@\"";

	const USER_NOT_ADMIN = "USER_NOT_ADMIN;USER;User [@USER@] is not admin";

	const ROLE_NAME_ALREADY_EXISTS = "ROLE_NAME_ALREADY_EXISTS;;A role with the same name already exists";

	const PERMISSION_ITEM_NOT_FOUND = "PERMISSION_ITEM_NOT_FOUND;;Permission item does not exists";

	const PROPERTY_DEPRECATED = "PROPERTY_DEPRECTAED;PROP;The property \"@PROP@\" is deprecated and should not be used";

	const PROPERTY_IS_NOT_DEFINED = "PROPERTY_IS_NOT_DEFINED;PROP,TYPE;The property \"@PROP@\" is not defined on type \"@TYPE@\"";
	
	const GROUPS_CANNOT_CO_EXIST = "GROUPS_CANNOT_CO_EXIST;userId,group,blockedCoExist;Cannot add user [@userId@] to group [@group@], User is already member of a group with coexistence enforcement [@blockedCoExist@]";

	/*
	 * syndication service
	 */
	const INVALID_XSLT = "INVALID_XSLT;;Invalid xslt";

	const INVALID_XSLT_MISSING_TEMPLATE_RSS = "INVALID_XSLT_MISSING_TEMPLATE_RSS;;Invalid xslt, missing rss's template tag with the following convention: xsl:template name=\"rssx\" match=\"/\"";

	const INVALID_XSLT_MISSING_TEMPLATE_ITEM = "INVALID_XSLT_MISSING_TEMPLATE_ITEM;;Invalid xslt, missing item's template tag with the following convention: xsl:template name=\"item\" match=\"item\"";

	const INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM = "INVALID_XSLT_MISSING_APPLY_TEMPLATES_ITEM;;Invalid xslt, missing apply-template tag for item's template with the following convention: xsl:apply-templates name=\"item\"";

	const SYNDICATION_FEED_INVALID_STORAGE_ID = "SYNDICATION_FEED_INVALID_STORAGE_ID;;Invalid storage id";

	const SYNDICATION_FEED_KALTURA_DC_ONLY = "SYNDICATION_FEED_KALTURA_DC_ONLY;;Partner configured to use Kaltura data centers only";

	const ENFORCE_ITUNES_FEED_AUTHOR = "ENFORCE_ITUNES_FEED_AUTHOR;;Missing feedAuthor param [Mandatory when enforcing feedAuthor]";

	/*
	 * file sync
	 */
	const FILE_DOESNT_EXIST = "FILE_DOESNT_EXIST;;File doesnt exist";

	const FILE_NOT_FOUND = "FILE_NOT_FOUND;;File not found";

	const STORAGE_PROFILE_ID_NOT_FOUND = "STORAGE_PROFILE_ID_NOT_FOUND;ID;Storage profile id @ID@ not found";

	const STORAGE_PROFILE_RULES_NOT_FULFILLED = "STORAGE_PROFILE_RULES_NOT_FULFILLED;ID;Storage profile rules for profile id @ID@ are not fulfilled";

	const FILE_PENDING = "FILE_PENDING;;File is pending";

	const FILE_TYPE_NOT_SUPPORTED = "FILE_TYPE_NOT_SUPPORTED;TYPE;File type @TYPE@ is not supported";

	/*
	 * resetUserPassword
	 */
	const CANNOT_RESET_PASSWORD_FOR_SYSTEM_PARTNER = "CANNOT_RESET_PASSWORD_FOR_SYSTEM_PARTNER;;Password cannot be reset for system partner";

	/*
	 * Report service
	 */
	const REPORT_NOT_FOUND = "REPORT_NOT_FOUND;ID;Report id \"@ID@\" not found";

	const REPORT_NOT_PUBLIC = "REPORT_NOT_PUBLIC;ID;Report id \"@ID@\" is not public";

	const REPORT_PARAMETER_MISSING = "REPORT_PARAMETER_MISSING;PARAM;Parameter \"@PARAM@\" is missing";

	const SEARCH_TOO_GENERAL = "SEARCH_TOO_GENERAL;;Unable to create report. Query produced too many results";

	const INVALID_REPORT_ITEMS_GROUP = "INVALID_REPORT_ITEMS_GROUP;;Invalid report items group";


	/**
	 * user service
	 */
	const INVALID_ID = "INVALID_ID;ID;Id \"@ID@\" contains invalid chars";

	/**
	 * categoryUser service
	 */
	const INVALID_CATEGORY_USER_ID = "INVALID_CATEGORY_USER_ID;CAT,USER;Invalid CategoryUser for category [\"@CAT@\"] and for user [\"@USER@\"]";

	const CATEGORY_USER_ALREADY_EXISTS = "CATEGORY_USER_ALREADY_EXISTS;;CategoryUser already exists";

	const CATEGORY_INHERIT_MEMBERS = "CATEGORY_INHERIT_MEMBERS;CAT;Cannot add members to this category since its inherit members from parent category [\"@CAT@\"]";

	const CATEGORY_INHERIT_MEMBERS_MUST_SET_PARENT_CATEGORY = "CATEGORY_INHERIT_MEMBERS_MUST_SET_PARENT_CATEGORY;;Category that inherit members must have parent category set";

	const CATEGORY_USER_JOIN_NOT_ALLOWED = "CATEGORY_USER_JOIN_NOT_ALLOWED;CAT;cannot register to this category [\"@CAT@\"]";

	const CANNOT_UPDATE_CATEGORY_USER = "CANNOT_UPDATE_CATEGORY_USER;;cannot update category user";

	const MUST_FILTER_USERS_OR_CATEGORY = "MUST_FILTER_USERS_OR_CATEGORY;;Must filter users or categories";

	const CANNOT_OVERRIDE_MANUAL_CHANGES = "CANNOT_OVERRIDE_MANUAL_CHANGES;;Cannot override manual changes";

	const CANNOT_UPDATE_CATEGORY_USER_OWNER = "CANNOT_UPDATE_CATEGORY_USER_OWNER;;Cannot change CategoryUser object for category Owner";

	/**
	 * entry
	 */

	const ENTRY_CATEGORY_FIELD_IS_DEPRECATED = "ENTRY_CATEGORY_FIELD_IS_DEPRECATED;;entry->categories and entry->categoriesIds fields are deprecated - user categoryEntry service";

	const ENTRY_DISPLAY_IN_SEARCH_VALUE_NOT_ALLOWED = "ENTRY_DISPLAY_IN_SEARCH_VALUE_NOT_ALLOWED;DISPLAY_IN_SEARCH_NEW_VALUE;Cannot set the value of DISPLAY_IN_SEARCH to [\"@DISPLAY_IN_SEARCH_NEW_VALUE@\"]";

	/**
	 * categoryEntry
	 */
	const INVALID_ENTRY_ID ="INVALID_ENTRY_ID;ID;Invalid entry id [\"@ID@\"]";

	const CANNOT_ASSIGN_ENTRY_TO_CATEGORY = "CANNOT_ASSIGN_ENTRY_TO_CATEGORY;;Cannot assign entry to category";

	const CANNOT_REMOVE_ENTRY_FROM_CATEGORY = "CANNOT_REMOVE_ENTRY_FROM_CATEGORY;;Cannot remove entry from category";

	const CANNOT_ACTIVATE_CATEGORY_ENTRY = "CANNOT_ACTIVATE_CATEGORY_ENTRY;;Cannot activate categoryEntry";

	const CANNOT_ACTIVATE_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING = "CANNOT_ACTIVATE_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING;;Cannot activate a non pending categoryEntry";

	const CANNOT_REJECT_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING = "CANNOT_REJECT_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING;;Cannot reject a non pending categoryEntry";

	const CANNOT_REJECT_CATEGORY_ENTRY = "CANNOT_REJECT_CATEGORY_ENTRY;;Cannot reject category entry";

	const ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY = "ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY;;Entry doesn't assigned to category";

	const MUST_FILTER_ENTRY_ID_EQUAL = "MUST_FILTER_ENTRY_ID_EQUAL;;Must filter on entry id";

	const MUST_FILTER_ON_ENTRY_OR_CATEGORY = "MUST_FILTER_ON_ENTRY_OR_CATEGORY;;Must filter on entry id or category";

	const CATEGORY_ENTRY_ALREADY_EXISTS = "CATEGORY_ENTRY_ALREADY_EXISTS;;Entry already assigned to this category";

	const CATEGORY_IS_LOCKED = "CATEGORY_IS_LOCKED;;Category is locked - cannot delete or change parent id";

	const CATEGORY_MAX_USER_REACHED = "CATEGORY_MAX_USER_REACHED;MAX;Max amount of users per category @MAX@ has been reached";

	/**
	 * Entitlement
	 */
	const CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE = "CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE;;Cannot index object when enetitlment is enabled";

	const CANNOT_LIST_RELATED_ENTITLED_WHEN_ENTITLEMENT_IS_ENABLE = "CANNOT_LIST_RELATED_ENTITLED_WHEN_ENTITLEMENT_IS_ENABLE;FILTER;Objects that require entitlement should not be listed [@FILTER@] as related-objects when enetitlment is enabled";

	const USER_KS_CANNOT_LIST_RELATED_ENTRIES = "USER_KS_CANNOT_LIST_RELATED_ENTRIES;FILTER;Entries should not be listed [@FILTER@] as related-objects with unprivileged user ks";

	// live stream
	const LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED = "LIVE_STREAM_STATUS_CANNOT_BE_DETERMINED;PROT;Status cannot be determined for live stream protocol [@PROT@]";

	const ENCODING_IP_NOT_PINGABLE = "ENCODING_IP_NOT_PINGABLE;;One or both of the provided encoding IPs is not pingable";

	const EXTENDING_ITEM_INCOMPATIBLE_COMBINATION = "EXTENDING_ITEM_INCOMPATIBLE_COMBINATION;;This extending object MRSS must replace the XPath contents";

	const EXTENDING_ITEM_MISSING_XPATH = "EXTENDING_ITEM_MISSING_XPATH;;Extending item must contain xpath";

	const LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND = 'LIVE_CHANNEL_SEGMENT_ID_NOT_FOUND;SEGMENT_ID;Live channel segment id [@SEGMENT_ID@] not found';

	const LIVE_STREAM_INVALID_TOKEN = "LIVE_STREAM_INVALID_TOKEN;ENTRY_ID;Invalid token supplied for live entry [@ENTRY_ID@]";

	const LIVE_STREAM_EXCEEDED_MAX_PASSTHRU = "LIVE_STREAM_EXCEEDED_MAX_PASSTHRU;ENTRY_ID;Partner exceeded max pass-through live streams in entry[@ENTRY_ID@]";

	const LIVE_STREAM_EXCEEDED_MAX_TRANSCODED = "LIVE_STREAM_EXCEEDED_MAX_TRANSCODED;ENTRY_ID;Partner exceeded max concurrent transcoded live streams in entry[@ENTRY_ID@]";

	const LIVE_STREAM_EXCEEDED_MAX_RTC_STREAMS = "LIVE_STREAM_EXCEEDED_MAX_RTC_STREAMS;PARTNER_ID,ALLOWED;Partner [@PARTNER_ID@] exceeded max concurrent rtc streams allowed [@ALLOWED@]";

	const LIVE_STREAM_EXCEEDED_MAX_CONCURRENT_BY_ADMIN_TAG = "LIVE_STREAM_EXCEEDED_MAX_CONCURRENT_BY_ADMIN_TAG;ENTRY_ID,ADMIN_TAG,ALLOWED;entry [@ENTRY_ID@] exceeded max concurrent streams of tag [@ADMIN_TAG@] (allowed [@ALLOWED@])";

	const LIVE_STREAM_EXCEEDED_MAX_RECORDED_DURATION = "LIVE_STREAM_EXCEEDED_MAX_RECORDED_DURATION;ENTRY_ID;Entry exceeded max recorded live stream duration in entry[@ENTRY_ID@]";

	const LIVE_STREAM_ALREADY_BROADCASTING = "LIVE_STREAM_ALREADY_BROADCASTING;ENTRY_ID,MEDIA_SERVER;Entry [@ENTRY_ID@] already broadcasting to server [@MEDIA_SERVER@]";

	const CANNOT_UPDATE_FIELDS_WHILE_ENTRY_BROADCASTING = "CANNOT_UPDATE_FIELDS_WHILE_ENTRY_BROADCASTING;FIELD;Cannot update [@FIELD@] while entry is broadcasting";

	const CANNOT_UPDATE_FIELDS_RECORDED_ENTRY_STILL_NOT_READY = "CANNOT_UPDATE_FIELDS_RECORDED_ENTRY_STILL_NOT_READY;FIELD;Cannot update [@FIELD@] while all vod entry flavors are not ready";

	const CANNOT_REGENERATE_STREAM_TOKEN_FOR_EXTERNAL_LIVE_STREAMS = "CANNOT_REGENERATE_STREAM_TOKEN_FOR_EXTERNAL_LIVE_STREAMS;TYPE;Cannot regenerate stream token for external type [@TYPE@] live stream";

	const KALTURA_RECORDING_ENABLED = "KALTURA_RECORDING_ENABLED;PARTNER_ID;Kaltura recording is enabled for partner [@PARTNER_ID@] use liveStream->setRecordedContent to set the live recorded content";

	const KALTURA_RECORDING_DISABLED = "KALTURA_RECORDING_DISABLED;PARTNER_ID;Kaltura recording is disabled for partner [@PARTNER_ID@] use liveStream->appendRecording to set the live recorded content";

	const RECORDING_DISABLED = "RECORDING_DISABLED;;Record status attribute cannot be set, account has recording feature disabled";

	const LIVE_CLIPPING_UNSUPPORTED_OPERATION = "LIVE_CLIPPING_UNSUPPORTED_OPERATION;OPERATION; Unsupported operation for live clipping: @OPERATION@";

	/*
	 * BaseEntry Service
	 */

	const DELIVERY_TYPE_NOT_SPECIFIED = "DELIVERY_TYPE_NOT_SPECIFIED;;At least one non auto delivery type must be specified";

	const SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED = "QUERY_EXCEEDED_MAX_MATCHES_ALLOWED;;Unable to generate list. max matches value was reached";

	const ASSIGNING_INFO_TO_ENTRY_WITH_PARENT_IS_FORBIDDEN = "ASSIGNING_INFO_TO_ENTRY_WITH_PARENT_IS_FORBIDDEN;ID;assigning categories|scheduling|access control to entry with parent entry \"@ID@\" is not allowed";

	const PARENT_ENTRY_ID_NOT_FOUND = "PARENT_ENTRY_ID_NOT_FOUND;ID;parent entry id \"@ID@\" not found";

	/*
	 * FileAsset Service
	 */

	const FILE_ASSET_ID_NOT_FOUND = "FILE_ASSET_ID_NOT_FOUND;ASSET_ID;File asset id [\"@ASSET_ID@\"] not found";

	/*
	 * MediaServer Service
	 */
	const MEDIA_SERVER_NOT_FOUND = "MEDIA_SERVER_NOT_FOUND;MEDIA_SERVER_ID;Media server [@MEDIA_SERVER_ID@] not found";

	const NO_MEDIA_SERVER_FOUND = "NO_MEDIA_SERVER_FOUND;ENTRY_ID;No media server found for entry [@ENTRY_ID@]";

	const MEDIA_SERVER_SERVICE_NOT_FOUND = "MEDIA_SERVER_SERVICE_NOT_FOUND;MEDIA_SERVER_ID,SERVICE;Media server [@MEDIA_SERVER_ID@] service [@SERVICE@] not found";

	/*
    * Delivery Service
    */
	const DELIVERY_ID_NOT_FOUND = 'DELIVERY_ID_NOT_FOUND;DELIVERY_ID;delivery id [@DELIVERY_ID@] not found';
	const DELIVERY_UPDATE_ISNT_ALLOWED = 'DELIVERY_UPDATE_ISNT_ALLOWED;DELIVERY_ID;delivery id [@DELIVERY_ID@] is default and can\'t be set';

	/*
	 * Live reports Service
	 */
	const LIVE_REPORTS_WS_FAILURE = 'LIVE_REPORTS_WS_FAILURE;;failed to retrieve live analytics';

	/*
	 * Analytics Service
	 */
	const ANALYTICS_QUERY_FAILURE = 'REPORTS_QUERY_FAILURE;ERROR_MSG;Failed to retrieve analytics data';
	const ANALYTICS_FORBIDDEN_FILTER = 'REPORTS_FORBIDDEN_FILTER;;Forbidden filter for dimension "partner" - queries are implicitly performed for the current partner';
	const ANALYTICS_INCORRECT_INPUT_TYPE = 'ANALYTICS_INCORRECT_INPUT_TYPE;;Ensure Content-Type is set to application/json';
	const ANALYTICS_INCORRECT_INPUT = 'ANALYTICS_INCORRECT_INPUT;ERRONOUS_FIELD;Error parsing field - [@ERRONOUS_FIELD@]';
	const ANALYTICS_UNSUPPORTED_DIMENSION = 'ANALYTICS_UNSUPPORTED_DIMENSION;DIMENSION;Dimension [@DIMENSION@] is not supported';
	const ANALYTICS_UNSUPPORTED_QUERY = 'ANALYTICS_UNSUPPORTED_QUERY;;Query for the given dimensions and metrics is currently not supported';


	/*
	 * Response Profiles
	 */
	const RESPONSE_PROFILE_NAME_NOT_FOUND = 'RESPONSE_PROFILE_NAME_NOT_FOUND;SYSTEM_NAME;Response profile name [@SYSTEM_NAME@] not found';

	const RESPONSE_PROFILE_ID_NOT_FOUND = 'RESPONSE_PROFILE_ID_NOT_FOUND;ID;Response profile id [@ID@] not found';

	const RESPONSE_PROFILE_DUPLICATE_SYSTEM_NAME = 'RESPONSE_PROFILE_DUPLICATE_SYSTEM_NAME;SYSTEM_NAME;Response profile system-name [@SYSTEM_NAME@] already exists';

	const RESPONSE_PROFILE_MAX_NESTING_LEVEL = 'RESPONSE_PROFILE_MAX_NESTING_LEVEL;;Response profile cross maximum nesting level';

	const RESPONSE_PROFILE_CACHE_NOT_FOUND = 'RESPONSE_PROFILE_MAX_NESTING_LEVEL;KEY;Response-Profile key [@KEY@] not found in cache';

	const RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED = 'RESPONSE_PROFILE_CACHE_ALREADY_RECALCULATED;;Response-Profile cache was recalculated already by a different process';

	const RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED = 'RESPONSE_PROFILE_CACHE_RECALCULATE_RESTARTED;;Response-Profile cache recalculate was restarted by a different process';

	/*
	 * User-Entry Service
	 */
	const USER_ENTRY_NOT_FOUND = 'USER_ENTRY_NOT_FOUND;USER_ENTRY_ID; User-Entry id [@USER_ENTRY_ID@] not found';
	const USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID = 'USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID;USER_ENTRY_ID;The entry id in the user-entry [@USER_ENTRY_ID@] does not match the entry-id given';
	const USER_ENTRY_OBJECT_TYPE_ERROR = 'USER_ENTRY_OBJECT_TYPE_ERROR;OBJ_TYPE,USER_ENTRY_ID;There is an error in the DB, object type [@OBJ_TYPE@] of UserEntry id [@USER_ENTRY_ID@] is unknown';
	const MUST_FILTER_ON_ENTRY_OR_USER = 'MUST_FILTER_ON_ENTRY_OR_USER;;Must filter on entry ID or user ID';
	const USER_ENTRY_FILTER_FORBIDDEN_FIELDS_USED = 'USER_ENTRY_FILTER_FORBIDDEN_FIELDS_USED;;UserEntry filter object forbidden fields used';
	const USER_ENTRY_ALREADY_EXISTS = 'USER_ENTRY_ALREADY_EXISTS;;UserEntry for this type already exists';
	const USER_ID_NOT_PROVIDED_OR_EMPTY = 'USER_ID_NOT_PROVIDED_OR_EMPTY;;User ID not found neither on the object or KS';

	/*
	 * serverNode service
	 */
	const HOST_NAME_ALREADY_EXISTS = "HOST_NAME_ALREADY_EXISTS;HOST_NAME;Host Name [@HOST_NAME@] already exists";
	const SERVER_NODE_NOT_FOUND = "SERVER_NODE_NOT_FOUND;HOST_NAME;server node with host name [@HOST_NAME@] not found";
	const SERVER_NODE_PROVIDED_AS_PARENT_NOT_FOUND = "SERVER_NODE_PROVIDED_AS_PARENT_NOT_FOUND;NODE_IDS;The following parentIds where not found [@NODE_IDS@]";
	const SERVER_NODE_PARENT_LOOP_DETECTED = "SERVER_NODE_PARENT_LOOP_DETECTED;ROUTE;ParentId loop detected on route [@ROUTE@], validate parentId tree definition";
	const SERVER_NODE_NOT_FOUND_WITH_ID = "SERVER_NODE_NOT_FOUND;SERVER_NODE_ID;server node with id [@SERVER_NODE_ID@] not found";

	/*
	 * EntryServerNode service
	 */
	const ENTRY_SERVER_NODE_NOT_FOUND = "ENTRY_SERVER_NODE_NOT_FOUND;ENTRY_ID,SERVER_TYPE;Entry server node with entry id [@ENTRY_ID@] and server type [@SERVER_TYPE@] not found";
	const ENTRY_SERVER_NODE_MULTI_RESULT = "ENTRY_SERVER_NODE_NOT_FOUND;ENTRY_ID,SERVER_TYPE;There were several results for entry server node with entry id [@ENTRY_ID@] and server type [@SERVER_TYPE@]";
	const MUST_FILTER_ON_ENTRY_OR_SERVER_TYPE = "MUST_FILTER_ON_ENTRY_OR_SERVER_TYPE;;Must filter on entry id or server type";
	const ENTRY_SERVER_NODE_OBJECT_TYPE_ERROR = "ENTRY_SERVER_NODE_OBJECT_TYPE_ERROR;OBJ_TYPE,ENTRY_SERVER_NODE_ID;There is an error in the DB, object type [@OBJ_TYPE@] of EntryServerNode id [@ENTRY_SERVER_NODE_ID@] is unknown";

	/*
	 * UserScore service
	 */
	const USER_SCORE_PROPERTIES_FILTER_REQUIRED = "USER_SCORE_PROPERTIES_FILTER_REQUIRED;;UserScorePropertiesFilter is required";
	const USER_ID_EQUAL_REQUIRED = "USER_ID_EQUAL_REQUIRED;;userIdEqual is required";
	const GAME_OBJECT_ID_REQUIRED = "GAME_OBJECT_ID_REQUIRED;;gameObjectId is required";
	const GAME_OBJECT_TYPE_REQUIRED = "GAME_OBJECT_TYPE_REQUIRED;;gameObjectType is required";
	const USER_ID_NOT_FOUND = "USER_ID_NOT_FOUND;USER_ID;userId [@USER_ID@] not found";
	
	/*
	 * Redis
	 */
	const FAILED_INIT_REDIS_INSTANCE = "FAILED_INIT_REDIS_INSTANCE;;Failed to initialize Redis instance";
	
	/*
	 * OTP error
	 */
	const INVALID_OTP = 'INVALID_OTP;;OTP provided failed to validate';
	const MISSING_OTP = 'MISSING_OTP;;OTP is missing';
	const ERROR_IN_QR_GENERATION = 'ERROR_IN_QR_GENERATION;;Could not generate QR code';
	const ERROR_IN_SEED_GENERATION = 'ERROR_IN_SEED_GENERATION;;Could not handle new seed generation';
	const INVALID_HASH = 'INVALID_HASH;;hashKey is not valid';


	/*
	 * clip concat Error
	 */

	const CANNOT_CREATE_CLIP_FLAVOR_JOB = "CANNOT_CREATE_CLIP_FLAVOR_JOB;;cannot create clip, flavor convert batch job returned as null";

	/*
	 * Conf Control
	 */
	const MISSING_MAP_NAME = "MISSING_MAP_NAME;;Map name must be supplied";
	const CANNOT_PARSE_CONTENT = "CANNOT_PARSE_CONTENT;ERR,CONTENT;Error - [@ERR@] Cannot parse content - \r\n [@CONTENT@]";
	const CONF_CONTORL_ERROR = "CONF_CONTORL_ERROR;ERR;Conf control error - [@ERR@]";
	const MAP_DOES_NOT_EXIST = "MAP_DOES_NOT_EXIST;;Map does not exist";
	const MAP_ALREADY_EXIST = "MAP_ALREADY_EXIST;NAME,HOST;Map already exist for this map name {@NAME@} and host {@HOST@}";
	const MAP_CANNOT_BE_CREATED_ON_FILE_SYSTEM = "MAP_CANNOT_BE_CREATED_ON_FILE_SYSTEM;;Map cannnot be created on file system";
	const HOST_NAME_CONTAINS_ASTRIX = "HOST_NAME_CONTAINS_ASTRIX;HOST_NAME;Host name contains *, use # instead {@HOST_NAME@}";
	const CHANGE_DESCRIPTION_CANNOT_BE_EMPTY = "CHANGE_DESCRIPTION_CANNOT_BE_EMPTY;;Param changeDescription cannot be empty";
	const SEARCH_ITEM_TYPE_NOT_FOUND = 'SEARCH_ITEM_TYPE_NOT_FOUND;SEARCH_ITEM_TYPE,ELASTIC_FIELD_NAME; Search item type [@SEARCH_ITEM_TYPE@] not found for field: [@ELASTIC_FIELD_NAME@]';
}

