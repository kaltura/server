<?php

class kKavaNoResultsException extends kException
{
}

class kKavaBase extends kDruidBase
{
	// data sources
	const DATASOURCE_REALTIME = 'player-events-realtime';
	const DATASOURCE_HISTORICAL = 'player-events-historical';
	const DATASOURCE_ENTRY_LIFECYCLE = 'entry-lifecycle';
	const DATASOURCE_USER_LIFECYCLE = 'user-lifecycle';
	const DATASOURCE_BANDWIDTH_USAGE = 'bandwidth-usage';
	const DATASOURCE_STORAGE_USAGE = 'storage-usage';
	const DATASOURCE_TRANSCODING_USAGE = 'transcoding-usage';
	const DATASOURCE_REACH_USAGE = 'reach-usage';
	const DATASOURCE_API_USAGE = 'api-usage';

	// dimensions
	const DIMENSION_PARTNER_ID = 'partnerId';
	const DIMENSION_PARTNER_PARENT_ID = 'partnerParentId';
	const DIMENSION_ENTRY_ID = 'entryId';
	const DIMENSION_LOCATION_COUNTRY = 'location.country';
	const DIMENSION_LOCATION_REGION = 'location.region';
	const DIMENSION_LOCATION_CITY = 'location.city';
	const DIMENSION_LOCATION_ISP = 'location.isp';
	const DIMENSION_DOMAIN = 'urlParts.domain';
	const DIMENSION_URL = 'urlParts.canonicalUrl';
	const DIMENSION_KUSER_ID = 'kuserId';
	const DIMENSION_USER_TYPE = 'userType';
	const DIMENSION_APPLICATION = 'application';
	const DIMENSION_DEVICE = 'userAgent.device';
	const DIMENSION_OS = 'userAgent.operatingSystem';
	const DIMENSION_OS_FAMILY = 'userAgent.operatingSystemFamily';
	const DIMENSION_BROWSER = 'userAgent.browser';
	const DIMENSION_BROWSER_FAMILY = 'userAgent.browserFamily';
	const DIMENSION_PLAYBACK_CONTEXT = 'playbackContext';
	const DIMENSION_PLAYBACK_TYPE = 'playbackType';
	const DIMENSION_SERVER_NODE_IDS = 'serverNodeIds';
	const DIMENSION_CATEGORIES = 'categories';
	const DIMENSION_EVENT_TYPE = 'eventType';
	const DIMENSION_HAS_BITRATE = 'hasBitrate';
	const DIMENSION_MEDIA_TYPE = 'mediaType';
	const DIMENSION_SOURCE_TYPE = 'sourceType';
	const DIMENSION_STATUS = 'status';
	const DIMENSION_SERVICE_TYPE = 'serviceType';
	const DIMENSION_SERVICE_FEATURE = 'serviceFeature';
	const DIMENSION_TURNAROUND_TIME = 'turnaroundTime';
	const DIMENSION_REACH_PROFILE_ID = 'reachProfileId';
	const DIMENSION_CUSTOM_VAR1 = 'customVar1';
	const DIMENSION_CUSTOM_VAR2 = 'customVar2';
	const DIMENSION_CUSTOM_VAR3 = 'customVar3';
	const DIMENSION_TYPE = 'type';
	const DIMENSION_ENTRY_OWNER_ID = 'entryKuserId';
	const DIMENSION_ENTRY_CREATOR_ID = 'entryCreatorId';
	const DIMENSION_ENTRY_CREATED_AT = 'entryCreatedAt';
	const DIMENSION_PERCENTILES = 'percentiles';
	const DIMENSION_EVENT_VAR1 = 'eventVar1';
	const DIMENSION_EVENT_VAR2 = 'eventVar2';
	const DIMENSION_EVENT_VAR3 = 'eventVar3';
	const DIMENSION_USER_ENGAGEMENT = 'userEngagement';
	const DIMENSION_EVENT_PROPERTIES = 'eventProperties';
	const DIMENSION_FLAVOR_PARAMS_ID = 'flavorParamsId';
	const DIMENSION_PLAYER_VERSION = 'playerVersion';
	const DIMENSION_POSITION = 'position';
	const DIMENSION_ROOT_ENTRY_ID = 'rootEntryId';

	// metrics
	const METRIC_COUNT = 'count';
	const METRIC_BUFFER_TIME_SUM = 'bufferTimeSum';
	const METRIC_BITRATE_SUM = 'bitrateSum';
	const METRIC_EVENT_DOUBLE_SUM1 = 'eventDoubleSum1';

	// playback types
	const PLAYBACK_TYPE_VOD = 'vod';
	const PLAYBACK_TYPE_LIVE = 'live';
	const PLAYBACK_TYPE_DVR = 'dvr';

	// event types - player events
	const EVENT_TYPE_PLAYER_IMPRESSION = 'playerImpression';
	const EVENT_TYPE_PLAY_REQUESTED = 'playRequested';
	const EVENT_TYPE_PLAY = 'play';
	const EVENT_TYPE_RESUME = 'resume';
	const EVENT_TYPE_PLAYTHROUGH_25 = 'playThrough25';
	const EVENT_TYPE_PLAYTHROUGH_50 = 'playThrough50';
	const EVENT_TYPE_PLAYTHROUGH_75 = 'playThrough75';
	const EVENT_TYPE_PLAYTHROUGH_100 = 'playThrough100';
	const EVENT_TYPE_EDIT_CLICKED = 'editClicked';
	const EVENT_TYPE_SHARE_CLICKED = 'shareClicked';
	const EVENT_TYPE_SHARED = 'shared';
	const EVENT_TYPE_DOWNLOAD_CLICKED = 'downloadClicked';
	const EVENT_TYPE_REPORT_CLICKED = 'reportClicked';
	const EVENT_TYPE_PLAY_END = 'playEnd';
	const EVENT_TYPE_REPORT_SUBMITTED = 'reportSubmitted';
	const EVENT_TYPE_ENTER_FULL_SCREEN = 'enterFullscreen';
	const EVENT_TYPE_EXIT_FULL_SCREEN = 'exitFullscreen';
	const EVENT_TYPE_PAUSE = 'pauseClicked';
	const EVENT_TYPE_REPLAY = 'replay';
	const EVENT_TYPE_SEEK = 'seek';
	const EVENT_TYPE_RELATED_CLICKED = 'relatedClicked';
	const EVENT_TYPE_RELATED_SELECTED = 'relatedSelected';
	const EVENT_TYPE_CAPTIONS = 'captions';
	const EVENT_TYPE_SOURCE_SELECTED = 'sourceSelected';
	const EVENT_TYPE_INFO = 'info';
	const EVENT_TYPE_SPEED = 'speed';
	const EVENT_TYPE_FLAVOR_SWITCH = 'flavorSwitch';
	const EVENT_TYPE_BUFFER_START = 'bufferStart';
	const EVENT_TYPE_ERROR = 'error';
	const EVENT_TYPE_VIEW = 'view';
	const EVENT_TYPE_VIEW_PERIOD = 'viewPeriod';

	// event types - storage / entry lifecycle
	const EVENT_TYPE_STATUS = 'status';
	const EVENT_TYPE_PHYSICAL_ADD = 'physicalAdd';
	const EVENT_TYPE_PHYSICAL_DELETE = 'physicalDelete';
	const EVENT_TYPE_LOGICAL_ADD = 'logicalAdd'; 
	const EVENT_TYPE_LOGICAL_DELETE = 'logicalDelete';

	// view events
	const VIEW_EVENT_INTERVAL = 10;
	const VIEW_EVENT_PERIOD = 'PT10S';	
	
	// params
	const VOD_DISABLED_PARTNERS = "disabled_kava_vod_partners";
	const LIVE_DISABLED_PARTNERS = "disabled_kava_live_partners";

	// media types
	const MEDIA_TYPE_VIDEO = 'Video';
	const MEDIA_TYPE_AUDIO = 'Audio';
	const MEDIA_TYPE_IMAGE = 'Image';
	const MEDIA_TYPE_SHOW = 'Show';		// mix
	const MEDIA_TYPE_LIVE_STREAM = 'Live stream';
	const MEDIA_TYPE_LIVE_WIN_MEDIA = 'Live stream windows media';
	const MEDIA_TYPE_LIVE_REAL_MEDIA = 'Live stream real media';
	const MEDIA_TYPE_LIVE_QUICKTIME = 'Live stream quicktime';

	// Entry vendor task statuses
	const TASK_READY = "Ready";

	// event properties
	const PROPERTY_HAS_BITRATE = 'hasBitrate';
	const PROPERTY_IS_BUFFERING = 'isBuffering';
	const PROPERTY_HAS_BANDWIDTH = 'hasBandwidth';
	const PROPERTY_HAS_LATENCY = 'hasLatency';
	const PROPERTY_HAS_DROPPED_FRAMES_RATIO = 'hasDroppedFramesRatio';
	const PROPERTY_HAS_JOIN_TIME = 'hasJoinTime';

	//user engagement values
	const USER_ENGAGED = 'SoundOnTabFocused';

	//general values
	const VALUE_UNKNOWN = 'Unknown';

	protected static $datasources_dimensions = array(
		self::DATASOURCE_HISTORICAL => array(
			self::DIMENSION_EVENT_TYPE => 1,
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_ENTRY_OWNER_ID => 1,
			self::DIMENSION_ENTRY_CREATOR_ID => 1,
			self::DIMENSION_ENTRY_CREATED_AT => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
			self::DIMENSION_LOCATION_COUNTRY => 1,
			self::DIMENSION_LOCATION_REGION => 1,
			self::DIMENSION_LOCATION_CITY => 1,
			self::DIMENSION_BROWSER_FAMILY => 1,
			self::DIMENSION_BROWSER => 1,
			self::DIMENSION_OS_FAMILY => 1,
			self::DIMENSION_OS => 1,
			self::DIMENSION_DEVICE => 1,
			self::DIMENSION_DOMAIN => 1,
			self::DIMENSION_URL => 1,
			self::DIMENSION_APPLICATION => 1,
			self::DIMENSION_PLAYBACK_CONTEXT => 1,
			self::DIMENSION_PLAYBACK_TYPE => 1,
			self::DIMENSION_CUSTOM_VAR1 => 1,
			self::DIMENSION_CUSTOM_VAR2 => 1,
			self::DIMENSION_CUSTOM_VAR3 => 1,
			self::DIMENSION_EVENT_PROPERTIES => 1,
			self::DIMENSION_ROOT_ENTRY_ID => 1,
		),
		self::DATASOURCE_ENTRY_LIFECYCLE => array(
			self::DIMENSION_EVENT_TYPE => 1,
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_USER_TYPE => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
		),
		self::DATASOURCE_STORAGE_USAGE => array(
			self::DIMENSION_EVENT_TYPE => 1,
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
		),
		self::DATASOURCE_BANDWIDTH_USAGE => array(
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_ENTRY_OWNER_ID => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
			self::DIMENSION_LOCATION_COUNTRY => 1,
			self::DIMENSION_LOCATION_REGION => 1,
			self::DIMENSION_LOCATION_CITY => 1,
			self::DIMENSION_BROWSER_FAMILY => 1,
			self::DIMENSION_BROWSER => 1,
			self::DIMENSION_OS_FAMILY => 1,
			self::DIMENSION_OS => 1,
			self::DIMENSION_DEVICE => 1,
			self::DIMENSION_PLAYBACK_TYPE => 1,
			self::DIMENSION_STATUS => 1,
			self::DIMENSION_TYPE => 1,
		),
		self::DATASOURCE_TRANSCODING_USAGE => array(
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
			self::DIMENSION_STATUS => 1,
		),
		self::DATASOURCE_USER_LIFECYCLE => array(
			self::DIMENSION_EVENT_TYPE => 1,
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_USER_TYPE => 1,
		),
		self::DATASOURCE_API_USAGE => array(
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_APPLICATION => 1,
			self::DIMENSION_DOMAIN => 1,
			self::DIMENSION_LOCATION_COUNTRY => 1,
			self::DIMENSION_LOCATION_REGION => 1,
			self::DIMENSION_LOCATION_CITY => 1,
		),
		self::DATASOURCE_REACH_USAGE => array(
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_ENTRY_OWNER_ID => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
			self::DIMENSION_REACH_PROFILE_ID => 1,
			self::DIMENSION_STATUS => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_SERVICE_TYPE => 1,
			self::DIMENSION_SERVICE_FEATURE => 1,
			self::DIMENSION_TURNAROUND_TIME  => 1,
		),
		self::DATASOURCE_REALTIME => array(
			self::DIMENSION_EVENT_TYPE => 1,
			self::DIMENSION_PARTNER_ID => 1,
			self::DIMENSION_PARTNER_PARENT_ID => 1,
			self::DIMENSION_KUSER_ID => 1,
			self::DIMENSION_ENTRY_ID => 1,
			self::DIMENSION_CATEGORIES => 1,
			self::DIMENSION_ENTRY_OWNER_ID => 1,
			self::DIMENSION_MEDIA_TYPE => 1,
			self::DIMENSION_SOURCE_TYPE => 1,
			self::DIMENSION_LOCATION_COUNTRY => 1,
			self::DIMENSION_LOCATION_REGION => 1,
			self::DIMENSION_LOCATION_CITY => 1,
			self::DIMENSION_BROWSER_FAMILY => 1,
			self::DIMENSION_BROWSER => 1,
			self::DIMENSION_OS_FAMILY => 1,
			self::DIMENSION_OS => 1,
			self::DIMENSION_DEVICE => 1,
			self::DIMENSION_DOMAIN => 1,
			self::DIMENSION_URL => 1,
			self::DIMENSION_APPLICATION => 1,
			self::DIMENSION_PLAYBACK_CONTEXT => 1,
			self::DIMENSION_PLAYBACK_TYPE => 1,
			self::DIMENSION_SERVER_NODE_IDS => 1,
			self::DIMENSION_CUSTOM_VAR1 => 1,
			self::DIMENSION_CUSTOM_VAR2 => 1,
			self::DIMENSION_CUSTOM_VAR3 => 1,
			self::DIMENSION_EVENT_PROPERTIES => 1,
			self::DIMENSION_PLAYER_VERSION => 1,
			self::DIMENSION_POSITION => 1,
		),
	);

	protected static $sourceFromAdminTag = array(
		'kalturaclassroom' => 'Classroom Capture',
		'kalturacapture' => 'Kaltura Capture',
		'videomessage' => 'Kaltura Pitch',
		'kms-webcast-event' => 'Kaltura Webcast',
		'raptentry' => 'Interactive Video',
		'webexentry' => 'Webex',
		'zoomentry' => 'Zoom',
		'expressrecorder' => 'Express Recorder',
	);

	protected static $sourceTypes = array(
		0 => 'Other',
		1 => 'Upload',
		2 => 'Webcam',
		3 => 'Flickr',
		4 => 'Youtube',
		5 => 'Url',
		6 => 'Text',
		7 => 'Myspace',
		8 => 'Photobucket',
		9 => 'Jamendo',
		10 => 'Ccmixter',
		11 => 'Nypl',
		12 => 'Current',
		13 => 'Commons',
		20 => 'Kaltura',
		21 => 'Kaltura user clips',
		22 => 'Archive org',
		23 => 'Kaltura partner',
		24 => 'Metacafe',
		29 => 'Live stream akamai legacy',
		30 => 'Live stream manual',
		31 => 'Live stream akamai universal',
		32 => 'Live stream',
		33 => 'Live channel',
		34 => 'Recorded live stream',
		35 => 'Clip',
		36 => 'Recorded live stream',
		37 => 'Classroom Capture',
	);

	public static function getEntrySourceType($sourceType, $adminTags)
	{
		// check for specific admin tags
		$adminTags = explode(',', strtolower($adminTags));
		foreach ($adminTags as $adminTag)
		{
			$adminTag = trim($adminTag);
			if (isset(self::$sourceFromAdminTag[$adminTag]))
			{
				return self::$sourceFromAdminTag[$adminTag];
			}
		}

		// use the source type
		return self::$sourceTypes[$sourceType];
	}

	public static function isPartnerAllowed($partnerId, $serviceType) {
	    if (kConf::hasParam(self::DRUID_URL)) {
		if (!kConf::hasParam($serviceType))
			return true;
		return !in_array($partnerId, kConf::get($serviceType));
	    }
	    return false;
	}

	public static function getCoordinatesKey($items)
	{
		$key = implode('_', $items);
		return 'coord_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($key));
	}

	public static function parseCoordinates($coords)
	{
		return array_map('floatval', explode('/', $coords));
	}

	public static function getCoordinatesForKeys($keys)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_GEO_COORDINATES);
		if (!$cache)
		{
			return array();
		}

		return $cache->multiGet($keys);
	}

	protected static function roundUpToMultiple($num, $mult)
	{
		$rem = $num % $mult;
		if (!$rem)
		{
			return $num;
		}

		return $num - $rem + $mult;
	}

}
