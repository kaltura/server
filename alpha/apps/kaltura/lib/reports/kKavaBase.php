<?php

class kKavaNoResultsException extends kException
{
}

class kKavaBase extends kDruidBase
{
	// data sources
	const REALTIME_DATASOURCE = 'player-events-realtime';
	const HISTORICAL_DATASOURCE = 'player-events-historical';

	// dimensions
	const DIMENSION_PARTNER_ID = 'partnerId';
	const DIMENSION_ENTRY_ID = 'entryId';
	const DIMENSION_LOCATION_COUNTRY = 'location.country';
	const DIMENSION_LOCATION_REGION = 'location.region';
	const DIMENSION_LOCATION_CITY = 'location.city';
	const DIMENSION_DOMAIN = 'urlParts.domain';
	const DIMENSION_URL = 'urlParts.canonicalUrl';
	const DIMENSION_USER_ID = 'userId';
	const DIMENSION_APPLICATION = 'application';
	const DIMENSION_DEVICE = 'userAgent.device';
	const DIMENSION_OS = 'userAgent.operatingSystem';
	const DIMENSION_BROWSER = 'userAgent.browser';
	const DIMENSION_PLAYBACK_CONTEXT = 'playbackContext';
	const DIMENSION_PLAYBACK_TYPE = 'playbackType';
	const DIMENSION_CATEGORIES = 'categories';
	const DIMENSION_EVENT_TYPE = 'eventType';
	const DIMENSION_HAS_BITRATE = 'hasBitrate';

	// metrics
	const METRIC_COUNT = 'count';
	const METRIC_BUFFER_TIME_SUM = 'bufferTimeSum';
	const METRIC_BITRATE_SUM = 'bitrateSum';

	// playback types
	const PLAYBACK_TYPE_VOD = 'vod';
	const PLAYBACK_TYPE_LIVE = 'live';
	const PLAYBACK_TYPE_DVR = 'dvr';

	// event types
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
	const EVENT_TYPE_VIEW = 'view';

	// view events
	const VIEW_EVENT_INTERVAL = 10;
	const VIEW_EVENT_PERIOD = 'PT10S';	
	
	// params
	public const VOD_ALLOWED_PARTNERS = "kava_vod_partners";
	public const LIVE_ALLOWED_PARTNERS = "kava_live_partners";
	
	public static function isPartnerAllowed($partnerId, $serviceType) {
	    if (kConf::hasParam(self::DRUID_URL)) {
	        if (!kConf::hasParam($serviceType)) 
	            return true;
	        return in_array($partnerId, kConf::get($serviceType));
	    }
	    return false;
	}
}