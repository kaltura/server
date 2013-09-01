<?php
/**
 * @package Core
 * @subpackage errors
 */
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
	const MULTIREQUEST_PROXY_FAILED = 24;
	const BAD_QUERY = 25;
	const INVALID_FLAVOR_ASSET_TYPE = 26;
	const INVALID_TOKEN = 27;
	const EXPIRED_TOKEN = 28;
	const PROCESSING_FEED_REQUEST = 29;
	const SERVICE_ACCESS_CONTROL_RESTRICTED = 30;
	const KS_EXPIRED = 31;
	const INVALID_PARTNER = 32;
	const ILLEGAL_UI_CONF = 33;
	const EXCEEDED_RESTRICTED_IP = 34;
	
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
			self::MULTIREQUEST_PROXY_FAILED => "tried to dump not the first request",
			self::BAD_QUERY => "wrong query attributes",
			self::INVALID_FLAVOR_ASSET_TYPE => "requested flavor asset type is invalid",
			self::INVALID_TOKEN => "the supplied token is invalid",
			self::EXPIRED_TOKEN => "the supplied token is expired",
			self::PROCESSING_FEED_REQUEST => "the supplied feed is already being processed",
			self::SERVICE_ACCESS_CONTROL_RESTRICTED => "action restricted due to access-control",
			self::KS_EXPIRED => "The given KS has expired",
			self::INVALID_PARTNER => "The given partner isn't vaild for the request",
			self::ILLEGAL_UI_CONF => "The given UI conf is illegal",
			self::EXCEEDED_RESTRICTED_IP => "ip address is out of the restricted ip range",
	);
	
	public static function dieError($errorCode, $message = null)
	{
		$description = self::$errorDescriptionMap[$errorCode];
		$args = func_get_args();
		if(count($args) > 1)
		{
			array_shift($args);
			$description = @call_user_func_array('sprintf', array_merge(array($description), $args));
		}
		
		if($message)
			$description .= ", $message";
			
		KalturaLog::err("exiting on error $errorCode - $description");
		self::terminateDispatch();
		
		
		header("X-Kaltura:error-$errorCode");
		header("X-Kaltura-App: exiting on error $errorCode - $description");

		if ($errorCode != self::ACCESS_CONTROL_RESTRICTED && 
			$errorCode != self::IP_COUNTRY_BLOCKED)
			requestUtils::sendCachingHeaders(60);
		
		die();
	}
	
	public static function dieGracefully($message = null)
	{
		if (class_exists('KalturaLog') && !is_null($message)) 
			KalturaLog::err($message);
		
		self::terminateDispatch();
		die();
	}
	
	public static function terminateDispatch() 
	{
		if (class_exists('KalturaLog') && isset($GLOBALS["start"])) 
			KalturaLog::debug("Disptach took - " . (microtime(true) - $GLOBALS["start"]) . " seconds, memory: ".memory_get_peak_usage(true));
	}
}	
