<?php
/**
 * @package Core
 * @subpackage errors
 */
class APIErrors 
{
	/**
	 * For a given error code and optional args, compose a text message (embedding arguments
	 * if given) and return a response array containing the message and the given error code and args.
	 * 
	 * Sample usage:
	 *     $errorData = APIErrors::getErrorData( APIErrors::INVALID_KS, array( 'KSID', 'ERR_CODE', 'ERR_DESC' ) );
	 *     
	 * Sample usage from a generic function (e.g. from KalturaAPIError):
	 *     function KalturaAPIException( $errorString )
	 *     {
	 *         $args = func_get_args();
	 *         array_shift( $args );
	 *         $errorData = APIErrors::getErrorData( $errorString, $args );
	 *     }
	 *  
	 * @param string $errorString
	 * @return array(
	 * 	   <br>'code'    => The given error code (e.g. 'INTERNAL_SERVERL_ERROR'),
	 *     <br>'args'    => A map between the error's params and the given values from $errorArgs, e.g. array( 'KSID' => '1234' ), or an empty array in case of no params 
	 * 	   <br>'message' => Composed English message. Any placeholedrs will be replaced with the supplied args.
	 *   <br>)
	 */
	public static function getErrorData( $errorString, $errorArgsArray = array() )
	{
		$errorData = array();
	
		// The error string format is:
		//     "ERROR_CODE;OPTIONAL,COMMA,SEPARATED,VALUES;ERROR STRING TEMPLATE WITH OPTIONAL @PARAM@ VARS"
		//
		// Sample format w/o params:
		//     "INTERNAL_DATABASE_ERROR;;Internal database error"
		//
		// Sample format w/ params:
		//     "INTERNAL_SERVER_ERROR;ERR_TEXT;Internal server error @ERR_TEXT@"
		$components = explode(';', $errorString, 3);
		
		$errorData['code'] = $components[0];
		$message = $components[2];
	
		$argsDictionary = array(); // A map between the params from the error string and their given errorArgs counterparts
	
		if ( ! empty($components[1]) ) // Need to process arguments?
		{
			$paramNames = explode(',', $components[1]);
			$numParamNames = count($paramNames);
	
			// Create and fill the argsDictionary dictionary
			for ( $i = 0; $i < $numParamNames; $i++ )
			{
				// Map the arg's name to its value
				// NOTE: N/A means there was a mismatch in the number of supplied arguments (i.e. a bug in the calling code)
				$argsDictionary[ $paramNames[$i] ] = isset( $errorArgsArray[$i] ) ? strip_tags($errorArgsArray[$i]) : "N/A";
	
				// Replace the arg's placeholder with its value in the destination string
				$message = str_replace("@{$paramNames[$i]}@", $argsDictionary[ $paramNames[$i] ], $message);
			}
		}
	
		$errorData['message'] = $message;
		$errorData['args'] = $argsDictionary;
	
		return $errorData;
	}

	/**
	 * @param $errorString string
	 * @return string
	 */
	public static function getCode($errorString)
	{
		$data = self::getErrorData($errorString);
		return $data["code"];
	}

	/**
	 * @param $errorString string
	 * @return string
	 */
	public static function getMessage($errorString)
	{
		$data = self::getErrorData($errorString);
		return $data["message"];
	}
	
	//  ERR_TEXT - some text to display in the message
	const INTERNAL_SERVERL_ERROR = "INTERNAL_SERVER_ERROR;ERR_TEXT;Internal server error @ERR_TEXT@";
	
	const INTERNAL_DATABASE_ERROR = "INTERNAL_DATABASE_ERROR;;Internal database error";
	
	const SEARCH_ENGINE_QUERY_FAILED = "SEARCH_ENGINE_QUERY_FAILED;;Search engine query failed";

	const RETRIEVE_VOLUME_MAP_FAILED = "RETRIEVE_VOLUME_MAP_FAILED;;Could not retrieve volume map for the given Id";
	
	const SERVERL_ERROR = "SERVERL_ERROR;ERR_TEXT;Server error @ERR_TEXT@";
	
	const MISSING_KS ="MISSING_KS;;Missing KS. Session not established";
	// KS - the ks string, ERR_CODE - error code , ERR_DESC - error description
	const INVALID_KS ="INVALID_KS;KSID,ERR_CODE,ERR_DESC;Invalid KS [@KSID@]. Error [@ERR_CODE@,@ERR_DESC@]";
	
	// PID - partner_id
	const START_SESSION_ERROR = "START_SESSION_ERROR;PID;Error while starting session for partner [@PID@]";
	
	const PARTNER_CHANGE_ACCOUNT_DISABLED = "PARTNER_CHANGE_ACCOUNT_DISABLED;;Partner change account is disabled for current session";

	const INVALID_ACTIONS_LIMIT = "INVALID_ACTIONS_LIMIT;;Invalid actions limit";
	
	const PRIVILEGE_IP_RESTRICTION = "INVALID_IP_ADDRESS_RESTRICTION;; Invalid IP address restriction";
	
	const INVALID_SET_ROLE = "INVALID_SET_ROLE;;Invalid set role id";
	
	// MEDIA_SRC - media source
	const UNKNOWN_MEDIA_SOURCE = "UNKNOWN_MEDIA_SOURCE;MEDIA_SRC;Unknown media source [@MEDIA_SRC@]";
	
	const NO_ENTRIES_ADDED = "NO_ENTRIES_ADDED;;Added 0 entries";
	
	const KSHOW_DOES_NOT_EXISTS = "KSHOW_DOES_NOT_EXISTS;;Kshow doesn't exist";
	
	const MODERATION_OBJECT_NOT_EXISTS = "MODERATION_OBJECT_NOT_EXISTS;OBJ_TYPE;Object to moderate [@OBJ_TYPE@] does not exist in system";
	
	const MODERATION_ONLY_ENTRY = "MODERATION_ONLY_ENTRY;OBJ_TYPE;For now can moderate only objects of type [entry] and not [@OBJ_TYPE@]";
	
	const MODERATION_EMPTY_OBJECT = "MODERATION_EMPTY_OBJECT;;Please fill the moderation object";
	
	// PUSER - puser_id 
	const INVALID_USER_ID = "INVALID_USER_ID;PUSER;Unknown user [@PUSER@]";
	
	// SREEN_NAME - Screen name
	const DUPLICATE_USER_BY_SCREEN_NAME= "DUPLICATE_USER_BY_SCREEN_NAME;SREEN_NAME;User with screenName [@SREEN_NAME@] already exists in system";
	
	const DUPLICATE_USER_BY_ID= "DUPLICATE_USER_BY_ID;UID;User with id [@UID@] already exists in system";
	
	const DUPLICATE_USER_BY_LOGIN_ID = "DUPLICATE_USER_BY_LOGIN_ID;EMAIL;Loginable user with email [@EMAIL@] already exists in system";
	
	// PARAM_NAME - param_name
	const MANDATORY_PARAMETER_MISSING = "MANDATORY_PARAMETER_MISSING;PARAM_NAME;Mandatory parameter missing [@PARAM_NAME@]";
	
	// ID - notification_id
	const INVALID_NOTIFICATION_ID = "INVALID_NOTIFICATION_ID;ID;Unknown notification [@ID@]";
	
	// ----
	const NO_NOTIFICATIONS_UPDATED = "NO_NOTIFICATIONS_UPDATED;;No notifications updated.";
	
	// OBJ_TYPE - the type of the object forwhich to sent the notification
	const ERROR_CREATING_NOTIFICATION = "ERROR_CREATING_NOTIFICATION;OBJ_TYPE;Cannot find object of type [@OBJ_TYPE@].";
	
	// KSHOW_ID - kshow_id
	const INVALID_KSHOW_ID = "INVALID_KSHOW_ID;KSHOW_ID;Unknown kshow [@KSHOW_ID@]" ;
	
	// KSHOW_NAME - kshow name
	const DUPLICATE_KSHOW_BY_NAME = "DUPLICATE_KSHOW_BY_NAME;KSHOW_NAME;Kshow with name [@KSHOW_NAME@] already exists in system " ;
	
	// KSHOW_ID - kshow_id , VERSION - $desired_version
	const ERROR_KSHOW_ROLLBACK = "ERROR_KSOHW_ROLLBACK;KSHOW_ID,VERSION;Error while rollbacking kshow [@KSHOW_ID@] to version [@VERSION@]";
	
	const ENTRY_ID_NOT_FOUND = "ENTRY_ID_NOT_FOUND;ENTRY_ID;Entry id \"@ENTRY_ID@\" not found";

	const SUPPORTED_FLAVOR_NOT_EXIST = "SUPPORTED_FLAVOR_NOT_EXIST;ENTRY_ID;Could not find supported flavor for entry id \"@ENTRY_ID@\" ";

	const GIVEN_ID_NOT_SUPPORTED = "GIVEN_ID_NOT_SUPPORTED;;The action is not supported for the given id";

	// ENTRY_TYPE - type
	const INVALID_ENTRY_TYPE = "INVALID_ENTRY_TYPE;ENTRY_TYPE;source entry must be of type [@ENTRY_TYPE@]";
	
	// ENTRY_TYPE - the type (string) of the entry dvdProject / bubbles , ...
	// ENTRY_ID entry_id
	const INVALID_ENTRY_ID = "INVALID_ENTRY_ID;ENTRY_TYPE,ENTRY_ID;Unknown @ENTRY_TYPE@ [@ENTRY_ID@]" ;
	
	const ENTRIES_AMOUNT_EXCEEDED = "ENTRIES_AMOUNT_EXCEEDED;;Entries amount exceeded" ;
	
	const INVALID_ENTRY_IDS = "INVALID_ENTRY_IDS;ENTRY_IDS;Unknown entry ids [@ENTRY_IDS@]" ;
	
	// ENTRY_TYPE - the type (string) of the entry dvdProject / bubbles , ...
	// ENTRY_ID entry_id
	const INVALID_ENTRY = "INVALID_ENTRY;ENTRY_TYPE,ENTRY_ID;Unknown @ENTRY_TYPE@ [@ENTRY_ID@]" ;
	
	const INVALID_ENTRY_VERSION = "INVALID_ENTRY_VERSION;ENTRY_TYPE,ENTRY_ID,VERSION;Unknown @ENTRY_TYPE@ [@ENTRY_ID@] [@VERSION@]" ;
	
	const INVALID_KSHOW_AND_ENTRY_PAIR = "INVALID_KSHOW_AND_ENTRY_PAIR;KSHOW_ID,ENTRY_ID;Unknown Kshow [@KSHOW_ID@] and Entry [@ENTRY_ID@]" ;
	
	const NO_FIELDS_SET_FOR_GENERIC_ENTRY = "NO_FIELDS_SET_FOR_GENERIC_ENTRY;ENTRY_TYPE;Missing fiedls when adding entry of type @ENTRY_TYPE@ " ;
	
	const NO_FIELDS_SET_FOR_USER = "NO_FIELDS_SET_FOR_USER;;Missing fiedls when adding user" ;
	
	const NO_FIELDS_SET_FOR_WIDGET = "NO_FIELDS_SET_FOR_WIDGET;;Missing fiedls when adding widget" ;
	
	// KSHOW_ID - kshow_id
	const KSHOW_CLONE_FAILED = "KSHOW_CLONE_FAILED;KSHOW_ID;clone failed for kshow [@KSHOW_ID@]";
	
	// ENTRY_ID - entry_id_to_delete , KSHOW_ID - kshow_id
	const CANNOT_DELETE_ENTRY = "CANNOT_DELETE_ENTRY;ENTRY_ID,KSHOW_ID;Entry [@ENTRY_ID@] does not belong to kshow_id [@KSHOW_ID@] and will not be deleted";
	
	// FILE_NAME - file name
	const INVALID_FILE_NAME = "INVALID_FILE_NAME;FILE_NAME;Cannot find file @FILE_NAME@";

	const INVALID_FILE_TYPE = "INVALID_FILE_TYPE;FILE_NAME;Restricted file type for file @FILE_NAME@";
	
	const ADMIN_KUSER_NOT_FOUND = "ADMIN_KUSER_NOT_FOUND;;The data you entered is invalid";
	
	// UI_CONF_ID - ui_conf_id
	const INVALID_UI_CONF_ID = "INVALID_UI_CONF_ID;UI_CONF_ID;Unknown uiConf [@UI_CONF_ID@]";
	
	// UI_CONF_ID - ui_conf_id
	const UI_CONF_CLONE_FAILED = "UI_CONF_CLONE_FAILED;UI_CONF_ID;clone failed for ui_conf [@UI_CONF_ID@]";

	// UI_CONF_ID - ui_conf_id
	const ERROR_SETTING_FILE_PATH_FOR_UI_CONF = "ERROR_SETTING_FILE_PATH_FOR_UI_CONF;UI_CONF_ID;Error while setting path [@UI_CONF_ID@] for ui_conf.";
	
	// WIDGET_ID - widget_id
	const INVALID_WIDGET_ID = "INVALID_WIDGET_ID;WIDGET_ID;Unknown widget [@WIDGET_ID@]";
	
	const INVALID_UI_CONF_ID_FOR_WIDGET = "INVALID_UI_CONF_ID_FOR_WIDGET;UI_CONF_ID,WIDGET_ID;Unknown uiConf [@UI_CONF_ID@] for widget [@WIDGET_ID@]";
	
	// RANK - rank
	const INVALID_RANK = "INVALID_RANK;RANK;Bad rank [@RANK@]";
	
	// USER_ID - user_id , KSHOW_ID - kshow_id
	const USER_ALREADY_RANKED_KSHOW = "USER_ALREADY_RANKED_KSHOW;USER_ID,KSHOW_ID;User [@USER_ID@] alreay voted for kshow [@KSHOW_ID@]";
	
	const USER_ALREADY_EXISTS_BY_SCREEN_NAME = "USER_ALREADY_EXISTS_BY_SCREEN_NAME;SCREEN_NAME;User with screenName [@SCREEN_NAME@] already exists in system.";
	
	const NO_FIELDS_SET_FOR_PARTNER = "NO_FIELDS_SET_FOR_PARTNER;;Missing fiedls when adding partner" ;
	
	// ERR_TEXT - a more specific error from myPartnerRegistration - TODO - make the module use more specific error codes
	const PARTNER_REGISTRATION_ERROR = "PARTNER_REGISTRATION_ERROR;MESSAGE;Error while registering partner with message \"@MESSAGE@\"";

	// MEDIA_TYPE - media_type
	const SEARCH_UNSUPPORTED_MEDIA_TYPE = "SEARCH_UNSUPPORTED_MEDIA_TYPE;MEDIA_TYPE;Unsupported media type [@MEDIA_TYPE@]";

	// MEDIA_SRC - media_source
	const SEARCH_UNSUPPORTED_MEDIA_SOURCE = "SEARCH_UNSUPPORTED_MEDIA_SOURCE;MEDIA_SRC;Unsupported media source [@MEDIA_SRC@]";
	
	// URL - url
	const SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL = "SEARCH_UNKNOWN_MEDIA_SOURCE_FOR_URL;URL;Unknown media source for url [@URL@]";
	
	const START_WIDGET_SESSION_ERROR = "START_WIDGET_SESSION_ERROR;WIDGET_ID;error while starting session for widget id [@WIDGET_ID@]";
	
	// PID - partner_id
	const UNKNOWN_PARTNER_ID = "UNKNOWN_PARTNER_ID;PID;Unknown partner_id [@PID@]";
	
	//
	const CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES = "CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES;;One or more media files cannot be imported";
	
	//
	const ADULT_CONTENT = "ADULT_CONTENT;;Adult content, age verification required, Please choose another movie";
	
	const SANDBOX_ALERT = "SANDBOX_ALERT;;Sandbox demo can not be updated";
	
	const ROUGHCUT_NOT_FOUND = "ROUGHCUT_NOT_FOUND;;Roughcut not found";
	
	const FEATURE_FORBIDDEN = "FEATURE_FORBIDDEN;FEATURE;The usage of feature [@FEATURE@] is forbidden";
	
	const SERVICE_FORBIDDEN = "SERVICE_FORBIDDEN;SERVICE;The access to service [@SERVICE@] is forbidden";
	
	const SERVICE_FORBIDDEN_CONTENT_BLOCKED = "SERVICE_FORBIDDEN_CONTENT_BLOCKED;;The access to this service is forbidden since the specified partner is blocked";
	
	const SERVICE_FORBIDDEN_FULLY_BLOCKED = "SERVICE_FORBIDDEN_FULLY_BLOCKED;;The access to this service is forbidden since the specified partner is fully blocked";
	
	const SERVICE_FORBIDDEN_PARTNER_DELETED = "SERVICE_FORBIDDEN_PARTNER_DELETED;;The access to this service is forbidden since the specified partner is deleted";

	const USER_BLOCKED = "USER_BLOCKED;;this user is blocked";

	const SERVICE_ACCESS_CONTROL_RESTRICTED = "SERVICE_ACCESS_CONTROL_RESTRICTED;SERVICE;The access to service [@SERVICE@] is forbidden due to access control restriction";
	
	const PARTNER_ACCESS_FORBIDDEN = "PARTNER_ACCESS_FORBIDDEN;ACCESSING_PID,ACCESSED_PID;Partner [@ACCESSING_PID@] cannot access partner [@ACCESSED_PID@]";
	
	const ACCESS_FORBIDDEN_FROM_UNKNOWN_IP = "ACCESS_FORBIDDEN_FROM_UNKNOWN_IP;IP;Access forbidden from unknown ip [@IP@]";
	
	const INVALID_BATCHJOB_ID = "INVALID_BATCHJOB_ID;JOB_ID;[@JOB_ID@]" ;
	
	const NO_FIELDS_SET_FOR_CONVERSION_PROFILE = "NO_FIELDS_SET_FOR_CONVERSION_PROFILE;;Missing fiedls when adding ConversionProfile" ;
	
	const INVALID_FILE_EXTENSION = "INVALID_FILE_EXTENSION;;Invalid file extension";
	
	const UNABLE_TO_CONVERT_ENTRY = "UNABLE_TO_CONVERT_ENTRY;;Unable to convert entry";
	
	const NO_FILES_RECEIVED = "NO_FILES_RECEIVED;;No files recieved";
	
	const INVALID_FILE_FIELD = "INVALID_FILE_FIELD;FIELD;The file was send on invalid field, expecting [@FIELD@]";
	
	const NO_FIELDS_SET_FOR_UI_CONF = "NO_FIELDS_SET_FOR_UI_CONF;;Missing fiedls when adding uiconf" ;
	
	const INVALID_PLAYLIST_TYPE = "INVALID_PLAYLIST_TYPE;;Invalid playlist type";

	const CLONING_PATH_PLAYLIST_NOT_SUPPORTED = 'CLONING_PATH_PLAYLIST_NOT_SUPPORTED;;Cloning Path playlist is not supported';

	// SRC_FILE - source file to be converted and downloaded
	const DOWNLOAD_ERROR = "DOWNLOAD_ERROR;SRC_FILE;Cannot find source file [@SRC_FILE@] in archive";

	// TYPE - type requested for transcoding
	const INVALID_TRANSCODE_TYPE = "INVALID_TRANSCODE_TYPE;TYPE;Invalid transcode type [@TYPE@]";
	
	const INVALID_TRANSCODE_DATA = "INVALID_TRANSCODE_DATA;DATA;Invalid transcode data [@DATA@]";
	
	// FIELD_NAME - field name
	const INVALID_FIELD_VALUE = "INVALID_FIELD_VALUE;FIELD_NAME;value in field [@FIELD_NAME@] is not valid";
	
	const ADMIN_KUSER_WRONG_OLD_PASSWORD = "ADMIN_KUSER_WRONG_OLD_PASSWORD;;Old password is wrong";

	const INVALID_PARTNER_PACKAGE = "INVALID_PARTNER_PACKAGE;;Invalid package id";
	
	const CANNOT_DOWNGRADE_PACKAGE = "CANNOT_DOWNGRADE_PACKAGE;;Downgrading package is impossible";

	const CANNOT_USE_ENTRY_TYPE_AUTO_IN_IMPORT = "CANNOT_USE_ENTRY_TYPE_AUTO_IN_IMPORT;;entry_type -1 (Auto) can be used only when source is file (1)";
	
	const NO_FIELDS_SET_FOR_SEARCH_RESULT = "NO_FIELDS_SET_FOR_SEARCH_RESULT;;Missing fiedls when adding SearchResult" ;

	// JOB_ID - job id, PROC_LOC - processorLocation , PROC_NAME - processorName , SERIALIZED_JOB_ID - serialized job, ERR_TEXT - error text
	const UPDATE_EXCLUSIVE_JOB_FAILED = "UPDATE_EXCLUSIVE_JOB_FAILED;JOB_ID,PROC_LOC,PROC_NAME,SERIALIZED_JOB_ID,ERR_TEXT;Error while attempting to update job with id [@JOB_ID@] from scheduler [@PROC_LOC@] worker [@PROC_NAME@] batch [@SERIALIZED_JOB_ID@]\n[@ERR_TEXT@]";
	
	// JOB_ID - job id, KEY - key , SERIALIZED_JOB_ID - serialized job
	const UPDATE_EXCLUSIVE_JOB_WRONG_TYPE = "UPDATE_EXCLUSIVE_JOB_WRONG_TYPE;JOB_ID,KEY,SERIALIZED_JOB_ID;Attempting to update job of the wrong type with id [@JOB_ID@]\n key[@KEY@]\njob[@SERIALIZED_JOB_ID@]";
	
	// JOB_ID - job id
	const GET_EXCLUSIVE_JOB_WRONG_TYPE = "GET_EXCLUSIVE_JOB_WRONG_TYPE;OBJ_TYPE,JOB_ID;Attempting to get job of the wrong type [@OBJ_TYPE@] with id [@JOB_ID@]";
	
	// JOB_ID - job id, PROC_LOC - processorLocation , PROC_NAME - processorName
	const FREE_EXCLUSIVE_JOB_FAILED = "FREE_EXCLUSIVE_JOB_FAILED;JOB_ID,PROC_LOC,PROC_NAME;Error while attempting to free job with id [@JOB_ID@] from processorLocation [@PROC_LOC@] processorName [@PROC_NAME@]";
	
	// UI_CONF_ID - ui_conf_id
	const INVALID_EXCLUSIVE_JOB_ID = "INVALID_EXCLUSIVE_JOB_ID;UI_CONF_ID;Unknown job [@UI_CONF_ID@]";

	// FLAV_ASSET_ID - flavor asset id
	const INVALID_FLAVOR_ASSET_ID = "INVALID_FLAVOR_ASSET_ID;FLAV_ASSET_ID; Invalid flavor asset id @FLAV_ASSET_ID@";

	// THUMB_ASSET_ID - thumb asset id
	const INVALID_THUMB_ASSET_ID = "INVALID_THUMB_ASSET_ID;THUMB_ASSET_ID;Invalid thumbnail asset id @THUMB_ASSET_ID@";


	const FLAVOR_ASSET_IS_NOT_READY = "FLAVOR_ASSET_IS_NOT_READY;;The flavor asset is not ready";
	
	
	const SOURCE_FILE_NOT_FOUND = "SOURCE_FILE_NOT_FOUND;;The flavor source file not found";
	
	
	const SOURCE_FILE_REMOTE = "SOURCE_FILE_REMOTE;;The source file is remote, no local file found";
	
	
	const PARTNER_NOT_SET = "PARTNER_NOT_SET;; Partner not set";

	// PID - partner id
	const INVALID_PARTNER_ID = "INVALID_PARTNER_ID;PID;Invalid partner id \"@PID@\"";

	
	const BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR;;Unable to create file sync object for bulk upload csv";
	
	// BULK_UPLOAD_TYPE - bulk upload type
	const BULK_UPLOAD_BULK_UPLOAD_TYPE_NOT_VALID = "BULK_UPLOAD_BULK_UPLOAD_TYPE_NOT_VALID;BULK_UPLOAD_TYPE;Bulk upload type [@BULK_UPLOAD_TYPE@] not supported";
		
	const BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR;;Unable to create file sync object for bulk upload result";
	
	
	const BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR;;Unable to create file sync object for flavor conversion";
	
	// PID - partner id
	const INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH = "INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH;PID;Invalid access to PARTNER_SPECIFIC search from partner [@PID@]";
	
	// ENGINE_TYPE - engine type
	const INVALID_CONVERSION_ENGINE_TYPE = "INVALID_CONVERSION_ENGINE_TYPE;ENGINE_TYPE;Invalid Conversion Engine Type [@ENGINE_TYPE@]";
	
	// EMAIL - searched TO email address or ID
	const EMAIL_INGESTION_PROFILE_NOT_FOUND = "EMAIL_INGESTION_PROFILE_NOT_FOUND;EMAIL;No records found for [@EMAIL@]";
	
	// EMAIL - searched TO email address
	const EMAIL_INGESTION_PROFILE_EMAIL_EXISTS = "EMAIL_INGESTION_PROFILE_EMAIL_EXISTS;EMAIL;Another email profile already using this email address [@EMAIL@]";
	
	const INVALID_FILE_SYNC_ID = "INVALID_FILE_SYNC_ID;FILE_SYNC_ID;Invalid file sync id [@FILE_SYNC_ID@]";
	
	const INVALID_SEARCH_TEXT = "INVALID_SEARCH_TEXT;TEXT;Invalid search text [@TEXT@]";
	
	// admin kuser password related errors
	
	const PASSWORD_STRUCTURE_INVALID = "PASSWORD_STRUCTURE_INVALID;RULES;The password you entered has an invalid structure.\nPasswords must obey the following rules :\n@RULES@";
	
	const COMMON_PASSWORD_NOT_ALLOWED = "COMMON_PASSWORD;;Chosen password is a common password";
	
	const PASSWORD_ALREADY_USED = "PASSWORD_ALREADY_USED;;Chosen password has already been used";
	
	const PASSWORD_EXPIRED = "PASSWORD_EXPIRED;;Current password has expired";
	
	const LOGIN_BLOCKED = "LOGIN_BLOCKED;;You account is locked";
	
	const LOGIN_RETRIES_EXCEEDED = "LOGIN_RETRIES_EXCEEDED;;Maximum login retries exceeded. Your account has been locked and will not be available for 24 hours";
	
	const NEW_PASSWORD_HASH_KEY_INVALID = "NEW_PASSWORD_HASH_KEY_INVALID;;Given hash key is invalid";
	
	const NEW_PASSWORD_HASH_KEY_EXPIRED = "NEW_PASSWORD_HASH_KEY_EXPIRED;;Given hash key has expired";
	
	const CANT_UPDATE_PARAMETER = "CANT_UPDATE_PARAMETER;PARAM_NAME;The following parameter cannot be updated [@PARAM_NAME@]";
	
	const LOGIN_DATA_NOT_FOUND = "LOGIN_DATA_NOT_FOUND;;Login id not found";
	
	const WRONG_OLD_PASSWORD = "WRONG_OLD_PASSWORD;;old password is wrong";
	
	const ADMIN_USER_PASSWORD_MISSING = "ADMIN_USER_PASSWORD_MISSING;;Admin user must have a password";
	
	const ADMIN_LOGIN_USERS_QUOTA_EXCEEDED = "ADMIN_LOGIN_USERS_QUOTA_EXCEEDED;;Partner login users quota exceeded";
	
	const USER_ALREADY_EXISTS = "USER_ALREADY_EXISTS;;User already exists";
	
	const CANNOT_UPDATE_LOGIN_DATA = "CANNOT_UPDATE_LOGIN_DATA;;Login data cannot be updated by this action";
	
	const CANNOT_UPDATE_ADMIN_LOGIN_DATA = "CANNOT_UPDATE_ADMIN_LOGIN_DATA;;Login data cannot be updated by this action";

	const LOGIN_ID_ALREADY_USED = "LOGIN_ID_ALREADY_USED;;Same login id is already in use";
	
	const USER_DATA_ERROR = "USER_DATA_ERROR;;User data is not valid.";
	
	const CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER = "CANNOT_DELETE_OR_BLOCK_ROOT_ADMIN_USER;;Root admin user cannot be deleted";
	
	const ROLE_ID_MISSING = "ROLE_ID_MISSING;;User must have an associated role";
	
	const UNKNOWN_ROLE_ID = "UNKNOWN_ROLE_ID;;Unknown role id";
	
	const UNKNOWN_ROLE_SYSTEM_NAME = "UNKNOWN_ROLE_SYSTEM_NAME;;Unknown role system name";
	
	const ONLY_ONE_ROLE_PER_USER_ALLOWED = "ONLY_ONE_ROLE_PER_USER_ALLOWED;;User cannot have more than one role";
	
	const CANNOT_SET_ROOT_ADMIN_AS_NO_ADMIN = "CANNOT_SET_ROOT_ADMIN_AS_NO_ADMIN;;Root admin user cannot be set to not admin";

	const CANNOT_CHANGE_OWN_ROLE = "CANNOT_CHANGE_OWN_ROLE;;User cannot change his own role";

	const NOT_ALLOWED_TO_CHANGE_ROLE = "NOT_ALLOWED_TO_CHANGE_ROLE;;User Is not allowed change roles";
	
	const PERMISSION_NOT_FOUND = "PERMISSION_NOT_FOUND;ERR_TEXT;@ERR_TEXT@";
	
	const PERMISSION_ALREADY_EXISTS = "PERMISSION_ALREADY_EXISTS;PERMISSION,PID;Permission [@PERMISSION@] already exists for partner [@PID@]";

	const USER_IS_BLOCKED = "USER_IS_BLOCKED;;User is blocked";
	
	const ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE = "ACCOUNT_OWNER_NEEDS_PARTNER_ADMIN_ROLE;;Account owner must have a partner administrator role";
	
	const ROLE_IS_BEING_USED = "ROLE_IS_BEING_USED;;Role is being used";
	
	const CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER = "CANNOT_DISABLE_LOGIN_FOR_ADMIN_USER;;Login cannot be disabled for admin users";
	
	const USER_ROLE_NOT_FOUND = "USER_ROLE_NOT_FOUND;;User role not found";
	
	const USER_WRONG_PASSWORD = "USER_WRONG_PASSWORD;;Wrong password supplied";
	
	const INVALID_FEED_ID = "INVALID_FEED_ID;FEED_ID;Unknown feed [@FEED_ID@]" ;
	
	const INVALID_FEED_TYPE = "INVALID_FEED_TYPE;FEED_TYPE;Wrong feed type [@FEED_TYPE@]" ;

	const ERROR_OCCURED_WHILE_GZCOMPRESS_JOB_DATA = "ERROR_OCCURED_WHILE_GZCOMPRESS_JOB_DATA;; error accored while gzcompress job data";
	
	const ERROR_OCCURED_WHILE_GZUNCOMPRESS_JOB_DATA = "ERROR_OCCURED_WHILE_GZUNCOMPRESS_JOB_DATA;; error accored while gzuncompress job data";

	const OBJECT_NOT_FOUND = "OBJECT_NOT_FOUND;;Object not found";
	
	const UNKNOWN_RESPONSE_FORMAT = "UNKNOWN_RESPONSE_FORMAT;FORMAT;Response format provided [@FORMAT@] is not recognized by server";
	
	const PROFILE_STATUS_DISABLED = "PROFILE_STATUS_DISABLED;PROFILE_ID;Export action failed since profile [@PROFILE_ID@] is disabled";

	const UNSAFE_HTML_TAGS = "UNSAFE_HTML_TAGS;CLASS_NAME,PROPERTY_NAME;Potential Unsafe HTML tags found in [@CLASS_NAME@]::[@PROPERTY_NAME@]";
	
	const RECORDED_NOT_READY = "RECORDED_NOT_READY;ENTRY_ID;Live entry [@ENTRY_ID@] cannot be deleted, recorded entry not in ready status yet";
	
	const RECORDING_FLOW_NOT_COMPLETE = "RECORDING_FLOW_NOT_COMPLETE;ENTRY_ID;Live entry [@ENTRY_ID@] cannot be deleted, recording engine still proccesing live event";
	
	const RECORDED_ENTRY_LIVE_MISMATCH = "RECORDED_ENTRY_LIVE_MISMATCH;LIVE_ENTRY_ID,RECORDED_ENTRY_ID;Recorded entry [@RECORDED_ENTRY_ID@] was not created while streaming to the given live entry [@LIVE_ENTRY_ID@]";
	
	const CANNOT_DELETE_LIVE_ENTRY_WHILE_STREAMING = "CANNOT_DELETE_LIVE_ENTRY_WHILE_STREAMING;LIVE_ENTRY_ID;Live entry [@LIVE_ENTRY_ID@] cannot be deleted while streaming";
	
	const RECORDING_CONTENT_NOT_YET_SET = "RECORDING_CONTENT_NOT_YET_SET;ENTRY_ID;Entry [@ENTRY_ID@] cannot be deleted, waiting for recording engine to finish handling";

	const USER_EMAIL_NOT_FOUND = "USER_EMAIL_NOT_FOUND;USER_ID;Email address for user [@USER_ID@] was not found";

	const FILE_CREATION_FAILED = "FILE_CREATION_FAILED;MESSAGE;Failed to create file on specified location with message: \"@MESSAGE@\"";

	const DRUID_QUERY_TIMED_OUT = "DRUID_QUERY_TIMED_OUT;;Query timed out";

	const NEW_LOGIN_REQUIRED = 'NEW_LOGIN_REQUIRED;;Switching to requested partner requires re-login';

	const DUPLICATE_LIVE_FEATURE = "DUPLICATE_LIVE_FEATURE;SYSTEM_NAME;Duplicate system name \"@SYSTEM_NAME@\" in provided live feature array";
}
