<?php
/**
 * @FIXME - refactor the current error codes to another exception class which will inherit from kCoreException
 * 
 * @package Core
 * @subpackage errors
 */
class kCoreException extends Exception
{
	/**
	 * Exception additional data
	 * @var string
	 */
	private $data;
	
	public function __construct($message, $code = null, $data = null)
	{
		$this->message = $message;
		$this->code = $code;
		$this->data = $data;
		KalturaLog::err($this);
	}
	
	/**
	 * Exception additional data
	 * @return string
	 */
	public function getData()
	{
		return $this->data;
	}
	
	const INVALID_QUERY = "INVALID_QUERY";

	const QUERY_NOT_FOUND = "QUERY_NOT_FOUND";

	const SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED = "SPHINX_CRITERIA_EXCEEDED_MAX_MATCHES_ALLOWED";
	
	const INVALID_ENUM_FORMAT = "INVALID_ENUM_FORMAT";
	
	const ENUM_NOT_FOUND = "ENUM_NOT_FOUND";
	
	const DUPLICATE_CATEGORY = "DUPLICATE_CATEGORY";

	const CATEGORY_NOT_FOUND = "CATEGORY_NOT_FOUND";

	const CATEGORY_ENTRY_ALREADY_EXISTS = "CATEGORY_ENTRY_ALREADY_EXISTS";

	const CANNOT_ASSIGN_ENTRY_TO_CATEGORY = "CANNOT_ASSIGN_ENTRY_TO_CATEGORY";
	
	const PARENT_ID_IS_CHILD = "PARENT_ID_IS_CHILD";
	
	const MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED = "MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED";
	
	const MAX_CATEGORIES_PER_ENTRY = "MAX_CATEGORIES_PER_ENTRY";
	
	const MAX_ASSETS_PER_ENTRY = "MAX_ASSETS_PER_ENTRY";
	
	const INTERNAL_SERVER_ERROR = "INTERNAL_SERVER_ERROR";
	
	const OBJECT_TYPE_NOT_FOUND = "OBJECT_TYPE_NOT_FOUND";
	
	const OBJECT_API_TYPE_NOT_FOUND = "OBJECT_API_TYPE_NOT_FOUND";
	
	const SOURCE_FILE_NOT_FOUND = "SOURCE_FILE_NOT_FOUND";
	
	const FILE_NOT_FOUND = "FILE_NOT_FOUND";

	const FILE_PENDING = "FILE_PENDING";
	
	const ACCESS_CONTROL_CANNOT_DELETE_PARTNER_DEFAULT = "ACCESS_CONTROL_CANNOT_DELETE_PARTNER_DEFAULT";
	
	const ACCESS_CONTROL_CANNOT_DELETE_USED_PROFILE = "ACCESS_CONTROL_CANNOT_DELETE_USED_PROFILE";
	
	const INVALID_USER_ID = "INVALID_USER_ID";
	
	const INVALID_ENTRY_ID = "INVALID_ENTRY_ID";
	
	const SEARCH_TOO_GENERAL = "SEARCH_TOO_GENERAL";
	
	const ID_NOT_FOUND = 'ID_NOT_FOUND';
	
	const INVALID_ENTRY_TYPE = "INVALID_ENTRY_TYPE";
	
	const INVALID_KS = 'INVALID_KS';
	
	const TEMPLATE_PARTNER_COPY_LIMIT_EXCEEDED = 'TEMPLATE_PARTNER_COPY_LIMIT_EXCEEDED';
	
	const MISSING_MANDATORY_PARAMETERS = 'MISSING_MANDATORY_PARAMETERS';
	
	const PARTNER_BLOCKED = 'PARTNER_BLOCKED';

	const ACCESS_UNAUTHORIZED = 'ACCESS_UNAUTHORIZED';
	
	const USER_BLOCKED = 'USER_BLOCKED';

	const INVALID_XSLT = 'INVALID_XSLT';
	
	const LOCK_TIMED_OUT = 'LOCK_TIMED_OUT';
	
	const MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED = "MAX_FILE_SYNCS_FOR_OBJECT_PER_DAY_REACHED";
	
	const DISABLE_CATEGORY_LIMIT_MULTI_PRIVACY_CONTEXT_FORBIDDEN = 'DISABLE_CATEGORY_LIMIT_MULTI_PRIVACY_CONTEXT_FORBIDDEN';

	const PROFILE_STATUS_DISABLED = 'PROFILE_STATUS_DISABLED';
	
	const EXCEEDED_MAX_ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT = "EXCEEDED_MAX_ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT";
	
	const NO_DEFAULT_ACCESS_CONTROL = "NO_DEFAULT_ACCESS_CONTROL";
	
	const MEDIA_SERVER_NOT_FOUND = "MEDIA_SERVER_NOT_FOUND";

	const ENTRY_SERVER_NODE_NOT_FOUND = "ENTRY_SERVER_NODE_NOT_FOUND";

	const EXCEEDED_MAX_CUSTOM_DATA_SIZE = "EXCEEDED_MAX_CUSTOM_DATA_SIZE";

	const DRUID_QUERY_TIMED_OUT = "DRUID_QUERY_TIMED_OUT";

	const INVALID_HASH = "INVALID_HASH";

	const ENTRY_VENDOR_TASK_ITEM_COULD_NOT_BE_UPDATED = 'ENTRY_VENDOR_TASK_ITEM_COULD_NOT_BE_UPDATED';
	
	const CANNOT_REMOVE_ENTRY_FROM_CATEGORY = 'CANNOT_REMOVE_ENTRY_FROM_CATEGORY';
	
	const USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP = 'USER_APP_ROLE_NOT_ALLOWED_FOR_GROUP';
	
	const USER_ROLE_NOT_FOUND = 'USER_ROLE_NOT_FOUND';
}
