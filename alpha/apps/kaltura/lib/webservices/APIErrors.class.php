<?php
	class APIErrors 
	{
		//  %s - some text to display in the message
		const INTERNAL_SERVERL_ERROR = "INTERNAL_SERVER_ERROR,Internal server error %s";
		
		const SERVERL_ERROR = "SERVERL_ERROR,Server error %s";
		
		const MISSING_KS ="MISSING_KS,Missing KS. Session not established";
		// %s - the ks string, %s - error code , %s - error description
		const INVALID_KS ="INVALID_KS,Invalid KS [%s]. Error [%s,%s]";

		// %s - partner_id
		const START_SESSION_ERROR = "START_SESSION_ERROR,Error while starting session for partner [%s]"; 
		
		// $s - media source
		const UNKNOWN_MEDIA_SOURCE = "UNKNOWN_MEDIA_SOURCE,Unknown media source [%s]";
		
		const NO_ENTRIES_ADDED = "NO_ENTRIES_ADDED,Added 0 entries";
		
		const KSHOW_DOES_NOT_EXISTS = "KSHOW_DOES_NOT_EXISTS,Kshow doesn't exist";
		
		const MODERATION_OBJECT_NOT_EXISTS = "MODERATION_OBJECT_NOT_EXISTS,Object to moderate [%s] does not exist in system";
		
		const MODERATION_ONLY_ENTRY = "MODERATION_ONLY_ENTRY,For now can moderate only objects of type [entry] and not [%s]";
		
		const MODERATION_EMPTY_OBJECT = "MODERATION_EMPTY_OBJECT,Please fill the moderation object";
		
		// %s - puser_id 
		const INVALID_USER_ID = "INVALID_USER_ID,Unknown user [%s]";
		
		// %s - name
		const DUPLICATE_USER_BY_SCREEN_NAME= "DUPLICATE_USER_BY_SCREEN_NAME,User with screenName [%s] already exists in system";
		
		const DUPLICATE_USER_BY_ID= "DUPLICATE_USER_BY_ID,User with id [%s] already exists in system";
		
		// %s - param_name
		const MANDATORY_PARAMETER_MISSING = "MANDATORY_PARAMETER_MISSING,Mandatory parameter missing [%s]";
		
		// %s - notification_id
		const INVALID_NOTIFICATION_ID = "INVALID_NOTIFICATION_ID,Unknown notification [%s]";
		
		// ----
		const NO_NOTIFICATIONS_UPDATED = "NO_NOTIFICATIONS_UPDATED,No notifications updated.";
		
		// %s - the type of the object forwhich to sent the notification
		const ERROR_CREATING_NOTIFICATION = "ERROR_CREATING_NOTIFICATION,Cannot find object of type [%s].";
		
		// %s - kshow_id
		const INVALID_KSHOW_ID = "INVALID_KSHOW_ID,Unknown kshow [%s]" ;
		
		// %s - kshow name
		const DUPLICATE_KSHOW_BY_NAME = "DUPLICATE_KSHOW_BY_NAME,Kshow with name [%s] already exists in system " ;
		
		// %s - kshow_id , %s - $desired_version
		const ERROR_KSHOW_ROLLBACK = "ERROR_KSOHW_ROLLBACK,Error while rollbacking kshow [%s] to version [%s]";
		
		// %s - type
		const INVALID_ENTRY_TYPE = "INVALID_ENTRY_TYPE,source entry must be of type [%s]";
		
		// %s - the type (string) of the entry dvdProject / bubbles , ...
		// % entry_id
		const INVALID_ENTRY_ID = "INVALID_ENTRY_ID,Unknown %s [%s]" ;
		
		const INVALID_ENTRY_IDS = "INVALID_ENTRY_IDS,Unknown entry ids [%s]" ;
		
		const INVALID_ENTRY = "INVALID_ENTRY,Unknown %s [%s]" ;
		
		const INVALID_ENTRY_VERSION = "INVALID_ENTRY_VERSION,Unknown %s [%s] [%s]" ;
		
		const INVALID_KSHOW_AND_ENTRY_PAIR = "INVALID_KSHOW_AND_ENTRY_PAIR,Unknown Kshow [%s] and Entry [%s]" ;
		
		const NO_FIELDS_SET_FOR_GENERIC_ENTRY = "NO_FIELDS_SET_FOR_GENERIC_ENTRY,Missing fiedls when adding entry of type %s " ;
		
		const NO_FIELDS_SET_FOR_USER = "NO_FIELDS_SET_FOR_USER,Missing fiedls when adding user" ;
		
		const NO_FIELDS_SET_FOR_WIDGET = "NO_FIELDS_SET_FOR_WIDGET,Missing fiedls when adding widget" ;
		
		// %s - kshow_id
		const KSHOW_CLONE_FAILED = "KSHOW_CLONE_FAILED,clone failed for kshow [%s]";
		
		// %s - entry_id_to_delete , %s - kshow_id
		const CANNOT_DELETE_ENTRY = "CANNOT_DELETE_ENTRY,Entry [%s] does not belong to kshow_id [%s] and will not be deleted";
		
		// %s - file name
		const INVALID_FILE_NAME = "INVALID_FILE_NAME,Cannot find file %s";
		
		const ADMIN_KUSER_NOT_FOUND = "ADMIN_KUSER_NOT_FOUND,The data you entered is invalid";
		
		// %s - ui_conf_id
		const INVALID_UI_CONF_ID = "INVALID_UI_CONF_ID,Unknown uiConf [%s]";
		
		// %s - ui_conf_id
		const UI_CONF_CLONE_FAILED = "UI_CONF_CLONE_FAILED,clone failed for ui_conf [%s]";

		// %s - ui_conf_id
		const ERROR_SETTING_FILE_PATH_FOR_UI_CONF = "ERROR_SETTING_FILE_PATH_FOR_UI_CONF,Error while setting path [%s] for ui_conf.";
		
		// %s - widget_id
		const INVALID_WIDGET_ID = "INVALID_WIDGET_ID,Unknown widget [%s]";
		
		const INVALID_UI_CONF_ID_FOR_WIDGET = "INVALID_UI_CONF_ID_FOR_WIDGET,Unknown uiConf [%s] for widget [%s]";
		
		// %s - rank
		const INVALID_RANK = "INVALID_RANK,Bad rank [%s]";
		
		// %s - user_id , %s - kshow_id
		const USER_ALREADY_RANKED_KSHOW = "USER_ALREADY_RANKED_KSHOW,User [%s] alreay voted for kshow [%s]";
		
		const USER_ALREADY_EXISTS_BY_SCREEN_NAME = "USER_ALREADY_EXISTS_BY_SCREEN_NAME,User with screenName [%s] already exists in system.";
		
		const NO_FIELDS_SET_FOR_PARTNER = "NO_FIELDS_SET_FOR_PARTNER,Missing fiedls when adding partner" ;
		
		// %s - a more specific error from myPartnerRegistration - TODO - make the module use more specific error codes
		const PARTNER_REGISTRATION_ERROR = "PARTNER_REGISTRATION_ERROR,Error while registering partner: %s";
		
		// $s - media_type
		const SEARCH_UNSUPPORTED_MEDIA_TYPE = "SEARCH_UNSUPPORTED_MEDIA_TYPE,Unsupported media type [%s]";

		// $s - media_source
		const SEARCH_UNSUPPORTED_MEDIA_SOURCE = "SEARCH_UNSUPPORTED_MEDIA_SOURCE,Unsupported media source [%s]";
		
		// %s - url
		const SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL = "SEARCH_UNKNOWN_MEDIA_SOURCE_FOR_URL,Unknown media source for url [%s]";
		
		const START_WIDGET_SESSION_ERROR = "START_WIDGET_SESSION_ERROR,error while starting session for widget id [%s]";
		
		// %s - partner_id
		const UNKNOWN_PARTNER_ID = "UNKNOWN_PARTNER_ID,Unknown partner_id [%s]";
		
		//
		const CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES = "CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES,One or more media files cannot be imported";
		
		//
		const ADULT_CONTENT = "ADULT_CONTENT,Adult content, age verification required, Please choose another movie";
		
		const SANDBOX_ALERT = "SANDBOX_ALERT,Sandbox demo can not be updated";
		
		const ROUGHCUT_NOT_FOUND = "ROUGHCUT_NOT_FOUND,Roughcut not found";
		
		const SERVICE_FORBIDDEN = "SERVICE_FORBIDDEN,The access to this service is forbidden";
		
		const SERVICE_FORBIDDEN_PARTNER_DELETED = "SERVICE_FORBIDDEN_PARTNER_DELETED,The access to this service is forbidden since the specified partner is deleted";
		
		const PARTNER_ACCESS_FORBIDDEN = "PARTNER_ACCESS_FORBIDDEN,Partner [%s] cannot access partner [%s]";
		
		const ACCESS_FORBIDDEN_FROM_UNKNOWN_IP = "ACCESS_FORBIDDEN_FROM_UNKNOWN_IP,Access forbidden from unknown ip [%s]";
		
		const INVALID_BATCHJOB_ID = "INVALID_BATCHJOB_ID [%s]" ;
		
		const NO_FIELDS_SET_FOR_CONVERSION_PROFILE = "NO_FIELDS_SET_FOR_CONVERSION_PROFILE,Missing fiedls when adding ConversionProfile" ;
		
		const INVALID_FILE_EXTENSION = "INVALID_FILE_EXTENSION,Invalid file extension";
		
		const NO_FILES_RECEIVED = "NO_FILES_RECEIVED,No files recieved";
		
		const INVALID_FILE_FIELD = "INVALID_FILE_FIELD,The file was send on invalid field, expecting [%s]";
		
		const NO_FIELDS_SET_FOR_UI_CONF = "NO_FIELDS_SET_FOR_UI_CONF,Missing fiedls when adding uiconf" ;
		
		const INVALID_PLAYLIST_TYPE = "INVALID_PLAYLIST_TYPE,Invalid playlist type";
		
		// %s - source file to be converted and downloaded
		const DOWNLOAD_ERROR = "DOWNLOAD_ERROR,Cannot find source file [%s] in archive";

		// %s - type requested for transcoding
		const INVALID_TRANSCODE_TYPE = "INVALID_TRANSCODE_TYPE,Invalid transcode type [%s]";
		
		const INVALID_TRANSCODE_DATA = "INVALID_TRANSCODE_DATA,Invalid transcode data [%s]";
		
		// %s - field name
		const INVALID_FIELD_VALUE = "INVALID_FIELD_VALUE,value in field [%s] is not valid";
		
		const ADMIN_KUSER_WRONG_OLD_PASSWORD = "ADMIN_KUSER_WRONG_OLD_PASSWORD,old password is wrong";

		const INVALID_PARTNER_PACKAGE = "INVALID_PARTNER_PACKAGE,Invalid package id";
		
		const CANNOT_DOWNGRADE_PACKAGE = "CANNOT_DOWNGRADE_PACKAGE,Downgrading package is impossible";

		const CANNOT_USE_ENTRY_TYPE_AUTO_IN_IMPORT = "CANNOT_USE_ENTRY_TYPE_AUTO_IN_IMPORT,entry_type -1 (Auto) can be used only when source is file (1)";
		
		const NO_FIELDS_SET_FOR_SEARCH_RESULT = "NO_FIELDS_SET_FOR_SEARCH_RESULT,Missing fiedls when adding SearchResult" ;

		// %s - job id, %s - processorLocation , %s - processorName , %s - serialized job
		const UPDATE_EXCLUSIVE_JOB_FAILED = "UPDATE_EXCLUSIVE_JOB_FAILED,Error while attempting to update job with id [%s] from scheduler [%s] worker [%s] batch [%s]\n[%s]";
		
		// %s - job id, %s - key , %s - serialized job
		const UPDATE_EXCLUSIVE_JOB_WRONG_TYPE = "UPDATE_EXCLUSIVE_JOB_WRONG_TYPE,Attempting to update job of the wrong type with id [%s]\n key[%s]\njob[%s]";
		
		// %s - job id
		const GET_EXCLUSIVE_JOB_WRONG_TYPE = "GET_EXCLUSIVE_JOB_WRONG_TYPE,Attempting to get job of the wrong type with id [%s]";
		
		// %s - job id, %s - processorLocation , %s - processorName
		const FREE_EXCLUSIVE_JOB_FAILED = "FREE_EXCLUSIVE_JOB_FAILED,Error while attempting to free job with id [%s] from processorLocation [%s] processorName [%s]";
		
		// %s - ui_conf_id
		const INVALID_EXCLUSIVE_JOB_ID = "INVALID_EXCLUSIVE_JOB_ID,Unknown job [%s]";
	
		// %s - flavor asset id
		const INVALID_FLAVOR_ASSET_ID = "INVALID_FLAVOR_ASSET_ID, Invalid flavor asset id %s";

		
		const PARTNER_NOT_SET = "PARTNER_NOT_SET, Partner not set";

		// %s - partner id
		const INVALID_PARTNER_ID = "INVALID_PARTNER_ID,Invalid partner id \"%s\"";
	
		
		const BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CSV_FILE_SYNC_ERROR, Unable to create file sync object for bulk upload csv";
		
		
		const BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_RESULT_FILE_SYNC_ERROR, Unable to create file sync object for bulk upload result";
		
		
		const BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR = "BULK_UPLOAD_CREATE_CONVERT_FILE_SYNC_ERROR, Unable to create file sync object for flavor conversion";
		
		// %s - partner id
		const INVALID_ACCESS_TO_PARTNER_SPECIFIC_SEARCH = "Invalid access to PARTNER_SPECIFIC search from partner [%s]";
		
		// %s - engine type
		const INVALID_CONVERSION_ENGINE_TYPE = "INVALID_CONVERSION_ENGINE_TYPE, Invalid Conversion Engine Type [%s]";
		
		// %s - searched TO email address or ID
		const EMAIL_INGESTION_PROFILE_NOT_FOUND = "EMAIL INGESTION PROFILE NOT FOUND, no records found for [%s]";
		
		// %s - searched TO email address
		const EMAIL_INGESTION_PROFILE_EMAIL_EXISTS = "EMAIL INGESTION PROFILE EMAIL EXISTS, another email profile already using this email address [%s]";
		
		const INVALID_FILE_SYNC_ID = "INVALID_FILE_SYNC_ID, Invalid file sync id [%s]";
		
		const INVALID_SEARCH_TEXT = "INVALID_SEARCH_TEXT, Invalid search text [%s]";
		
		// admin kuser password related errors
		
		const PASSWORD_STRUCTURE_INVALID = "PASSWORD_STRUCTURE_INVALID,The password you entered has an invalid structure.\nPasswords must obey the following rules :\n- Must be of length between 8 and 14.\n- Must not contain your name\n- Must contain at least one lowercase letter (a-z).\n- Must contain at least one digit (0-9).\n- Must contain at least one of the following symbols:  %%~!@#\$^*=+?[]{}";
		
		const PASSWORD_ALREADY_USED = "PASSWORD_ALREADY_USED,Chosen password has already been used";
		
		const PASSWORD_EXPIRED = "PASSWORD_EXPIRED,Current password has expired";
		
		const LOGIN_BLOCKED = "LOGIN_BLOCKED,You account is locked";
		
		const LOGIN_RETRIES_EXCEEDED = "LOGIN_RETRIES_EXCEEDED,Maximum login retries exceeded. Your account has been locked and will not be available for 24 hours";
		
		const NEW_PASSWORD_HASH_KEY_INVALID = "NEW_PASSWORD_HASH_KEY_INVALID,Given hash key is invalid";
		
		const NEW_PASSWORD_HASH_KEY_EXPIRED = "NEW_PASSWORD_HASH_KEY_EXPIRED,Given has key has expired";
	}
?>