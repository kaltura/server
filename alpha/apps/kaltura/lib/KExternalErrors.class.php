<?php

/**
 * @package Core
 * @subpackage errors
 */
class KExternalErrors
{
	private static $responseCode = null;

	const CACHE_EXPIRY = 60;

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
	const INVALID_MAX_BITRATE = 19;
	const MISSING_PARAMETER = 20;
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
	const INVALID_FEED_ID = 35;
	const ENTRY_NOT_LIVE = 36;
	const INVALID_ISM_FILE_TYPE = 37;
	const NOT_ALLOWED_PARAMETER = 38;
	const INVALID_SETTING_TYPE = 39;
	const ACTION_BLOCKED = 40;
	const INVALID_HASH = 41;
	const PARENT_ENTRY_ID_NOT_FOUND = 42;
	const USER_NOT_FOUND = 43;
	const INTERNAL_SERVER_ERROR = 44;
	const LIVE_STREAM_CONFIG_NOT_FOUND = 45;
	const TOO_MANY_PROCESSES = 46;
	const BUNDLE_CREATION_FAILED = 47;
	const ENTRY_NOT_SEQUENCE = 48;
	const INVALID_MIN_BITRATE = 49;
	const INVALID_PARAMETER = 50;

	const HTTP_STATUS_NOT_FOUND = 404;

	private static $errorCodeMap = array(
		self::HTTP_STATUS_NOT_FOUND => "HTTP/1.0 404 Not Found",
	);

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
		self::INVALID_FEED_ID => "The given feed id is illegal",
		self::ENTRY_NOT_LIVE => "The given entry is not broadcasting",
		self::INVALID_ISM_FILE_TYPE => "The given ism file type is invalid",
		self::NOT_ALLOWED_PARAMETER => "The provided parameter is not allowed",
		self::INVALID_SETTING_TYPE => "Invalid setting type",
		self::ACTION_BLOCKED => "The requested action is blocked for this partner",
		self::INVALID_HASH => "Hash key contains invalid characters",
		self::PARENT_ENTRY_ID_NOT_FOUND => "Parent entry id provided not found in system",
		self::USER_NOT_FOUND => "The provided user id was not found",
		self::INTERNAL_SERVER_ERROR => "Internal server error",
		self::LIVE_STREAM_CONFIG_NOT_FOUND => "Live stream playback config not found for requested live entry",
		self::TOO_MANY_PROCESSES => "Too many executed processes",
		self::BUNDLE_CREATION_FAILED => "Failed to build bundle for [%s]",
		self::ENTRY_NOT_SEQUENCE => "One or more of the sequence entry ids given is not a sequence entry",
		self::INVALID_MIN_BITRATE => "min bitrate is not valid",
		self::INVALID_PARAMETER => "Request parameter [%s] is invalid",
	);

	public static function dieError($errorCode, $message = null)
	{
		$description = self::$errorDescriptionMap[$errorCode];
		$args = func_get_args();
		if (count($args) > 1) {
			array_shift($args);
			$description = @call_user_func_array('sprintf', array_merge(array($description), $args));
		}

		if ($message)
			$description .= ", $message";

		if (class_exists('KalturaLog'))
			KalturaLog::err("exiting on error $errorCode - $description");

		$headers = array();
		if (self::$responseCode)
			$headers[] = self::$errorCodeMap[self::$responseCode];

		$headers[] = "X-Kaltura-App: exiting on error $errorCode - $description";

		foreach ($headers as $header) {
			header($header);
		}
		header("X-Kaltura:error-$errorCode");

		$headers[] = "X-Kaltura:cached-error-$errorCode";

		self::terminateDispatch();

		if ($errorCode != self::ACCESS_CONTROL_RESTRICTED &&
			$errorCode != self::IP_COUNTRY_BLOCKED &&
			$_SERVER["REQUEST_METHOD"] == "GET"
		) {
			infraRequestUtils::sendCachingHeaders(self::CACHE_EXPIRY, true, time());

			if (function_exists('apc_store')) {
				$protocol = infraRequestUtils::getProtocol();
				$host = "";
				if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
					$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
				else if (isset($_SERVER['HTTP_HOST']))
					$host = $_SERVER['HTTP_HOST'];
				$uri = $_SERVER["REQUEST_URI"];
				apc_store("exterror-$protocol://$host$uri", $headers, self::CACHE_EXPIRY);
			}
		}

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
			KalturaLog::debug("Dispatch took - " . (microtime(true) - $GLOBALS["start"]) . " seconds, memory: " . memory_get_peak_usage(true));
	}

	public static function setResponseErrorCode($errorCode)
	{
		self::$responseCode = $errorCode;
	}
}
