<?php
class KExternalErrors
{
	const ENTRY_NOT_FOUND = 1;
	const NOT_SCHEDULED_NOW = 2;
	const ACCESS_CONTROL_RESTRICTED = 3;
	const INVALID_KS_SRT = 4;
	const FILE_NOT_FOUND = 5;
	const FLAVOR_NOT_FOUND = 6;
	const INVALID_KS = 7;
	const ENTRY_AND_WIDGET_NOT_FOUND = 8;
	const ENTRY_DELETED_MODERATED = 9;
	const MISSING_THUMBNAIL_FILESYNC = 10;
	const PROCESSING_CAPTURE_THUMBNAIL = 11;
	const INVALID_ENTRY_TYPE = 12;
	const ENTRY_MODERATION_ERROR = 13;
	const PARTNER_NOT_FOUND = 14;
	const PARTNER_NOT_ACTIVE = 15;
	const IP_COUNTRY_BLOCKED = 16;
	const IMAGE_RESIZE_FAILED = 17;
	const DELIVERY_METHOD_NOT_ALLOWED = 18;
	const INVALID_MAX_BITRATE= 19;
	const MISSING_PARAMETER= 20;
	const WIDGET_NOT_FOUND = 21;
	const UI_CONF_NOT_FOUND = 22;
	const PROXY_LOOPBACK = 23;
	
	private static $errorDescriptionMap = array(
			self::ENTRY_NOT_FOUND => "requested entry not found",
			self::NOT_SCHEDULED_NOW => "entry restricted due to scheduling",
			self::ACCESS_CONTROL_RESTRICTED => "entry restricted due to access-control",
			self::INVALID_KS_SRT => "ks is an invalid string",
			self::FILE_NOT_FOUND => "required file was not found",
			self::FLAVOR_NOT_FOUND => "requested flavor was not found",
			self::INVALID_KS => "ks is not valid",
			self::ENTRY_AND_WIDGET_NOT_FOUND => "no entry and no widget could be loaded",
			self::ENTRY_DELETED_MODERATED => "requested entry is deleted or rejected",
			self::MISSING_THUMBNAIL_FILESYNC => "missing thumbnail fileSync for entry",
			self::PROCESSING_CAPTURE_THUMBNAIL => "processing capture thumbnail",
			self::INVALID_ENTRY_TYPE => "requested entry type is invalid for the requested format",
			self::ENTRY_MODERATION_ERROR => "entry is restricted due to invalid moderation status",
			self::PARTNER_NOT_FOUND => "requested partner not found",
			self::PARTNER_NOT_ACTIVE => "requested partner not active",
			self::IP_COUNTRY_BLOCKED => "", // we rather not explain this error code 
			self::IMAGE_RESIZE_FAILED => "image resize failed",
			self::DELIVERY_METHOD_NOT_ALLOWED => "Delivery method not allowed",
			self::INVALID_MAX_BITRATE => "max bitrate is not valid",
			self::MISSING_PARAMETER => "Request parameter [%s] is missing",
			self::WIDGET_NOT_FOUND => "requested widget not found",
			self::UI_CONF_NOT_FOUND => "requested ui_conf not found",
			self::PROXY_LOOPBACK => "proxied request is being looped back",
			
		);
	
	public static function dieError($errorCode)
	{
		$description = self::$errorDescriptionMap[$errorCode];
		$args = func_get_args();
		if(count($args) > 1)
		{
			array_shift($args);
			$description = @call_user_func_array('sprintf', array_merge(array($description), $args));
		}
		
		KalturaLog::err("exiting on error $errorCode - $description");
		
		header("X-Kaltura:error-$errorCode");
		header("X-Kaltura-App: exiting on error $errorCode - $description");
		die();
	}
}	