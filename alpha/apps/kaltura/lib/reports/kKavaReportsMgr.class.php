<?php

class kKavaReportsMgr
{
    static $aggregations_def = array();
    static $metrics_def = array();
    
    const HISTORICAL_DATASOURCE = "player-events-historical";
    const DRUID_QUERY_TYPE = "queryType";
    const DRUID_TOPN = "topN";
    const DRUID_TIMESERIES = "timeseries";
    const DRUID_GROUP_BY = "groupBy";
    const DRUID_FILTERED_AGGR = "filtered";
    const DRUID_SELECTOR_FILTER = "selector";
    const DRUID_IN_FILTER = "in";
    const DRUID_TYPE = "type";
    const DRUID_FILTER = "filter";
    const DRUID_DIMENSION = "dimension";
    const DRUID_DIMENSIONS = "dimensions";
    const DRUID_VALUE = "value";
    const DRUID_VALUES = "values";
    const DRUID_ARITHMETIC_POST_AGGR = "arithmetic";
    const DRUID_FUNCTION = "fn";
    const DRUID_AGGREGATOR = "aggregator";
    const DRUID_NAME = "name";
    const DRUID_METRIC = "metric";
    const DRUID_THRESHOLD = "threshold";
    const DRUID_FIELD_NAME = "fieldName";
    const DRUID_LONG_SUM_AGGR = "longSum";
    const DRUID_GRANULARITY = "granularity";
    const DRUID_GRANULARITY_ALL = "all";
    const DRUID_GRANULARITY_DAY = "day";
    const DRUID_GRANULARITY_HOUR = "hour";
    const DRUID_DATASOURCE = "dataSource";
    const DRUID_INTERVALS = "intervals";
    const DRUID_FIELDS = "fields";
    const DRUID_CARDINALITY = "cardinality";
    const DRUID_HYPER_UNIQUE = "hyperUnique";
    const DRUID_POST_AGGR = "postAggregations";
    const DRUID_AGGR = "aggregations";
    const DRUID_FIELD_ACCESS = "fieldAccess";
    const DRUID_CONSTANT = "constant";
    const DRUID_GRANULARITY_PERIOD = "period";
    const DRUID_TIMEZONE = "timeZone";
    const DRUID_NUMERIC = "numeric";
    const DRUID_INVERTED = "inverted";
    
    private static $event_type_count_aggr_template = 
        array(self::DRUID_TYPE => self::DRUID_FILTERED_AGGR,
              self::DRUID_FILTER => array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER,
                                          self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
                                          self::DRUID_VALUE => "value"),
                                          self::DRUID_AGGREGATOR => array(self::DRUID_TYPE => self::DRUID_LONG_SUM_AGGR,
                                                                          self::DRUID_NAME => "name",
                                                                          self::DRUID_FIELD_NAME => "count"));
    
    private static $events_types_count_aggr_template = 
        array(self::DRUID_TYPE => self::DRUID_FILTERED_AGGR,
              self::DRUID_FILTER => array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER,
              self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
              self::DRUID_VALUES => "value"),
              self::DRUID_AGGREGATOR => array(self::DRUID_TYPE => self::DRUID_LONG_SUM_AGGR,
                                              self::DRUID_NAME => "name",
                                              self::DRUID_FIELD_NAME => "count"));
        
    private static $arithmetic_post_aggr_template = 
        array(self::DRUID_TYPE => self::DRUID_ARITHMETIC_POST_AGGR,
              self::DRUID_NAME => "name",
              self::DRUID_FUNCTION => "function",
    );
    
    private static $top_n_query_template = 
        array(self::DRUID_QUERY_TYPE => self::DRUID_TOPN,
              self::DRUID_DATASOURCE => self::HISTORICAL_DATASOURCE,
              self::DRUID_INTERVALS => "time_intervals",
              self::DRUID_GRANULARITY => self::DRUID_GRANULARITY_ALL,
              self::DRUID_DIMENSION => "dimension",
              self::DRUID_METRIC => "metric",
              self::DRUID_THRESHOLD => "threshold");
    
    private static $time_series_query_teample = 
        array(self::DRUID_QUERY_TYPE => self::DRUID_TIMESERIES,
              self::DRUID_DATASOURCE => self::HISTORICAL_DATASOURCE,
              self::DRUID_INTERVALS => "time_intervals",
              self::DRUID_GRANULARITY => "granularity");
    
    private static $group_by_query_template = 
        array(self::DRUID_QUERY_TYPE => self::DRUID_GROUP_BY,
              self::DRUID_DATASOURCE => self::HISTORICAL_DATASOURCE,
              self::DRUID_INTERVALS => "time_intervals",
              self::DRUID_GRANULARITY => self::DRUID_GRANULARITY_ALL,
              self::DRUID_DIMENSION => "dimension");
    
    static $reportsGranularity = 
        array("content_dropoff" => self::DRUID_GRANULARITY_ALL,
              "user_content_dropoff" => self::DRUID_GRANULARITY_ALL,
              "live" => self::DRUID_GRANULARITY_HOUR,
              "platforms_drilldown"=> self::DRUID_GRANULARITY_ALL,
              "os" => self::DRUID_GRANULARITY_ALL,
              "browsers" => self::DRUID_GRANULARITY_ALL);
    
    const DIMENSION_PARTNER_ID = "partnerId";
    const DIMENSION_ENTRY_ID = "entryId";
    const DIMENSION_LOCATION_COUNTRY = "location.country";
    const DIMENSION_LOCATION_CITY = "location.city";
    const DIMENSION_DOMAIN = "urlParts.domain";
    const DIMENSION_URL = "urlParts.canonicalUrl";
    const DIMENSION_USER_ID = "userId";
    const DIMENSION_APPLICATION = "application";
    const DIMENSION_DEVICE = "userAgent.device";
    const DIMENSION_OS = "userAgent.operatingSystem";
    const DIMENSION_BROWSER = "userAgent.browser";
    const DIMENSION_PLAYBACK_CONTEXT = "playbackContext";
    const DIMENSION_PLAYBACK_TYPE = "playbackType";
    const DIMENSION_CATEGORIES = "categories";
    const DIMENSION_EVENT_TYPE = "eventType";
    
    static $reportsDimensions = array("top_content" => self::DIMENSION_ENTRY_ID,
        "content_dropoff" => self::DIMENSION_ENTRY_ID,
        "content_interactions" => self::DIMENSION_ENTRY_ID,
        "map_overlay" => self::DIMENSION_LOCATION_COUNTRY,
        "map_overlay_drilldown" => self::DIMENSION_LOCATION_CITY,
        "top_syndication" => self::DIMENSION_DOMAIN,
        "top_syndication_drilldown" => self::DIMENSION_URL,
        "user_engagement" => self::DIMENSION_USER_ID,
        "specific_user_engagement" => self::DIMENSION_ENTRY_ID,
        "user_top_content" => self::DIMENSION_USER_ID,
        "user_content_dropoff" => self::DIMENSION_USER_ID,
        "user_content_interactions" => self::DIMENSION_USER_ID,
        "applications" => self::DIMENSION_APPLICATION,
        "platforms" => self::DIMENSION_DEVICE,
        "platforms_drilldown" => self::DIMENSION_OS,
        "os" => self::DIMENSION_OS,
        "os_drilldown" => self::DIMENSION_BROWSER,
        "browsers" => self::DIMENSION_BROWSER,
        "live" => self::DIMENSION_ENTRY_ID,
        "top_playback_context" => self::DIMENSION_PLAYBACK_CONTEXT);
    
    const EVENT_TYPE_PLAYER_IMPRESSION = "playerImpression";
    const EVENT_TYPE_PLAY_REQUESTED = "playRequested";
    const EVENT_TYPE_PLAY = "play";
    const EVENT_TYPE_RESUME = "resume";
    const EVENT_TYPE_PLAYTHROUGH_25 = "playThrough25";
    const EVENT_TYPE_PLAYTHROUGH_50 = "playThrough50";
    const EVENT_TYPE_PLAYTHROUGH_75 = "playThrough75";
    const EVENT_TYPE_PLAYTHROUGH_100 = "playThrough100";
    const EVENT_TYPE_EDIT_CLICKED = "editClicked";
    const EVENT_TYPE_SHARE_CLICKED = "shareClicked";
    const EVENT_TYPE_SHARED = "shared";
    const EVENT_TYPE_DOWNLOAD_CLICKED = "downloadClicked";
    const EVENT_TYPE_REPORT_CLICKED = "reportClicked";
    const EVENT_TYPE_PLAY_END = "playEnd";
    const EVENT_TYPE_REPORT_SUBMITTED = "reportSubmitted";
    const EVENT_TYPE_ENTER_FULL_SCREEN = "enterFullscreen";
    const EVENT_TYPE_EXIT_FULL_SCREEN = "exitFullscreen";
    const EVENT_TYPE_PAUSE = "pauseClicked";
    const EVENT_TYPE_REPLAY = "replay";
    const EVENT_TYPE_SEEK = "seek";
    const EVENT_TYPE_RELATED_CLICKED = "relatedClicked";
    const EVENT_TYPE_RELATED_SELECTED = "relatedSelected";
    const EVENT_TYPE_CAPTIONS = "captions";
    const EVENT_TYPE_SOURCE_SELECTED = "sourceSelected";
    const EVENT_TYPE_INFO = "info";
    const EVENT_TYPE_SPEED = "speed";
    const EVENT_TYPE_VIEW = "view";
    const METRIC_TOTAL_PLAY_TIME = "playTimeSum";
    const METRIC_AVG_PLAY_TIME = "playTimeAvg";
    const METRIC_PLAYER_IMPRESSION_RATIO = "playerImpressionRatio";
    const METRIC_AVG_DROP_OFF = "avgDropOffRatio";
    const METRIC_PLAYTHROUGH_RATIO = "playThroughRatio";
    const METRIC_TOTAL_ENTRIES = "totalEntries";
    const METRIC_UNIQUE_USERS = "uniqueUsers";
    const METRIC_UNIQUE_USER_IDS = "uniqueUserIds";
    const METRIC_PLAYTHROUGH = "playThrough";

    static $reportsMetrics = array("top_content" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
        "content_dropoff" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
        "content_interactions" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
        "map_overlay" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
        "map_overlay_drilldown" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
        "top_syndication" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "top_syndication_drilldown" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "user_engagement" => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME ,self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "specific_user_engagement" => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "user_top_content" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "user_content_dropoff" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
        "user_content_interactions" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
        "applications" => array(self::EVENT_TYPE_PLAYER_IMPRESSION),
        "platforms" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "os" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "browsers" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
        "live" => array(self::EVENT_TYPE_PLAY),
        "top_playback_context" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO));
    
    static $detailDimensionHeaders = array("top_content" => array("object_id", "entry_name"),
        "content_dropoff" => array("object_id", "entry_name"),
        "content_interactions" => array("object_id", "entry_name"),
        "map_overlay" => array("object_id", "country"),
        "map_overlay_drilldown" => array("object_id","location"),
        "top_syndication" => array("object_id","domain_name"),
        "top_syndication_drilldown" => array("referrer"),
        "user_engagement" => array("name"),
        "specific_user_engagement" => array("entry_name"),
        "user_top_content" => array("name"),
        "user_content_dropoff" => array("name"),
        "user_content_interactions" => array("name"),
        "applications" => array("name"),
        "platforms" => array("device"),
        "platforms_drilldown" => array("os"),
        "os" => array("os"),
        "os_drilldown" => array("browser"),
        "browsers" => array("browser"),
        "live" => array("object_id", "entry_name"),
        "top_playback_context" => array("object_id", "name"));
    
    static $metricsToHeaders = array(self::EVENT_TYPE_PLAY => "count_plays",
        self::METRIC_TOTAL_PLAY_TIME => "sum_time_viewed",
        self::METRIC_AVG_PLAY_TIME => "avg_time_viewed",
        self::EVENT_TYPE_PLAYER_IMPRESSION => "count_loads",
        self::METRIC_PLAYER_IMPRESSION_RATIO => "load_play_ratio",
        self::METRIC_AVG_DROP_OFF => "avg_view_drop_off",
        self::EVENT_TYPE_PLAYTHROUGH_25 => "count_plays_25",
        self::EVENT_TYPE_PLAYTHROUGH_50 => "count_plays_50",
        self::EVENT_TYPE_PLAYTHROUGH_75 => "count_plays_75",
        self::EVENT_TYPE_PLAYTHROUGH_100 => "count_plays_100",
        self::DIMENSION_DEVICE => "device",
        self::METRIC_TOTAL_ENTRIES => "unique_videos",
        self::METRIC_UNIQUE_USERS => "unique_known_users",
        self::EVENT_TYPE_REPORT_CLICKED => "count_report",
        self::EVENT_TYPE_DOWNLOAD_CLICKED => "count_download",
        self::EVENT_TYPE_SHARE_CLICKED => "count_viral",
        self::EVENT_TYPE_EDIT_CLICKED => "count_edit",
        self::METRIC_PLAYTHROUGH_RATIO => "play_through_ratio"
    );
    
    static $headersToMetrics = array();
    
    static $reportsGraphMetrics = array("top_content" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "content_dropoff" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100),
        "content_interactions" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),        
        "top_syndication" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "user_engagement" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF),
        "specific_user_engagement" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "user_top_content" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "user_content_dropoff" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
        "user_content_interactions" => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
        "applications" => array("application"),
        "platforms" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "os" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "browsers" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        "live" => array(self::EVENT_TYPE_PLAY),
        "top_playback_context" => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO));
    
    static $reportTotalAdditionalMetrics = array("user_engagement" => array(self::METRIC_UNIQUE_USERS),
        "specific_user_engagement" => array(self::METRIC_UNIQUE_USERS),
        "user_top_content" => array(self::METRIC_UNIQUE_USERS),
        "user_content_dropoff" => array(self::METRIC_UNIQUE_USERS),
        "user_content_interactions" => array(self::METRIC_UNIQUE_USERS)
    );
    
    static $reportsToEnrich = array("top_content" => "entry_name",
        "content_dropoff" => "entry_name",
        "content_interactions" => "entry_name",
        "live" => "entry_name",
        "top_playback_context" => "name",
        "specific_user_engagement" => "entry_name"
    );
    
    static $reportEnrichFunc = array("top_content" => "self::getEntriesNames",
        "content_dropoff" => "self::getEntriesNames",
        "content_interactions" => "self::getEntriesNames",
        "live" => "self::getEntriesNames",
        "specific_user_engagement" => "self::getEntriesNames",
        "top_playback_context" => "self::getCategoriesNames");
    
    static $transform_metrics = array(self::METRIC_TOTAL_ENTRIES, self::METRIC_UNIQUE_USERS);
    
    private static function getCountAggrTemplate($event_type) {
        $count_aggr = self::$event_type_count_aggr_template;
        $count_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = $event_type;
        $count_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = $event_type;
        
        return $count_aggr;
    }
    
    private static function getRatioAggTemplate($agg_name, $field1, $field2) 
    {
        $ratio_aggr = self::$arithmetic_post_aggr_template;
        $ratio_aggr[self::DRUID_NAME] = $agg_name;
        $ratio_aggr[self::DRUID_FUNCTION] = "/";
        $fields= array(array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => $field1, self::DRUID_FIELD_NAME => $field1),
            array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => $field2, self::DRUID_FIELD_NAME => $field2));
        $ratio_aggr[self::DRUID_FIELDS] = $fields;
        
        return $ratio_aggr;
        
    }
    
    private static function init() {
        
        if (!self::$metrics_def) 
        {    
            self::$metrics_def[self::EVENT_TYPE_PLAYER_IMPRESSION] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAY_REQUESTED] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAY] = array();
            self::$metrics_def[self::EVENT_TYPE_RESUME] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAYTHROUGH_25] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAYTHROUGH_50] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAYTHROUGH_75] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAYTHROUGH_100] = array();
            self::$metrics_def[self::METRIC_PLAYTHROUGH] = array();
            self::$metrics_def[self::EVENT_TYPE_EDIT_CLICKED] = array();
            self::$metrics_def[self::EVENT_TYPE_SHARE_CLICKED] = array();
            self::$metrics_def[self::EVENT_TYPE_SHARED] = array();
            self::$metrics_def[self::EVENT_TYPE_DOWNLOAD_CLICKED] = array();
            self::$metrics_def[self::EVENT_TYPE_REPORT_CLICKED] = array();
            self::$metrics_def[self::EVENT_TYPE_REPORT_SUBMITTED] = array();
            self::$metrics_def[self::EVENT_TYPE_ENTER_FULL_SCREEN] = array();
            self::$metrics_def[self::EVENT_TYPE_EXIT_FULL_SCREEN] = array();
            self::$metrics_def[self::EVENT_TYPE_PAUSE] = array();
            self::$metrics_def[self::EVENT_TYPE_REPLAY] = array();
            self::$metrics_def[self::EVENT_TYPE_SEEK] = array();
            self::$metrics_def[self::EVENT_TYPE_RELATED_CLICKED] = array();
            self::$metrics_def[self::EVENT_TYPE_RELATED_SELECTED] = array();
            self::$metrics_def[self::EVENT_TYPE_CAPTIONS] = array();
            self::$metrics_def[self::EVENT_TYPE_SOURCE_SELECTED] = array();
            self::$metrics_def[self::EVENT_TYPE_INFO] = array();
            self::$metrics_def[self::EVENT_TYPE_SPEED] = array();
            self::$metrics_def[self::EVENT_TYPE_VIEW] = array();
            self::$metrics_def[self::METRIC_TOTAL_ENTRIES] = array();
            self::$metrics_def[self::METRIC_TOTAL_PLAY_TIME] = array();
            self::$metrics_def[self::EVENT_TYPE_PLAY_END] = array();
            self::$metrics_def[self::METRIC_UNIQUE_USERS] = array();
            
            foreach (self::$metrics_def as $key => $value) {
                self::$metrics_def[$key] = array(self::DRUID_AGGR => array($key));
            }
            
            self::$metrics_def[self::METRIC_AVG_PLAY_TIME] = array(self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME, self::EVENT_TYPE_PLAY_END),
                self::DRUID_POST_AGGR => array(self::METRIC_AVG_PLAY_TIME));
            self::$metrics_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
                self::DRUID_POST_AGGR => array(self::METRIC_PLAYER_IMPRESSION_RATIO));
            self::$metrics_def[self::METRIC_AVG_DROP_OFF] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYTHROUGH),
                self::DRUID_POST_AGGR => array(self::METRIC_AVG_DROP_OFF));
            self::$metrics_def[self::METRIC_PLAYTHROUGH_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_100),
                self::DRUID_POST_AGGR => array(self::METRIC_PLAYTHROUGH_RATIO));
            
            self::$aggregations_def[self::EVENT_TYPE_PLAY] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAYER_IMPRESSION] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAY_END] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAYTHROUGH_25] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAYTHROUGH_50] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAYTHROUGH_75] = array();
            self::$aggregations_def[self::EVENT_TYPE_PLAYTHROUGH_100] = array();
            self::$aggregations_def[self::EVENT_TYPE_EDIT_CLICKED] = array();
            self::$aggregations_def[self::EVENT_TYPE_SHARE_CLICKED] = array();
            self::$aggregations_def[self::EVENT_TYPE_DOWNLOAD_CLICKED] = array();
            self::$aggregations_def[self::EVENT_TYPE_REPORT_CLICKED] = array();
            
            foreach (self::$aggregations_def as $key => $value) {
                self::$aggregations_def[$key] = self::getCountAggrTemplate($key);
            }
            
            self::$aggregations_def[self::METRIC_AVG_PLAY_TIME] = self::getRatioAggTemplate(self::METRIC_AVG_PLAY_TIME, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_TOTAL_PLAY_TIME);
            self::$aggregations_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = self::getRatioAggTemplate(self::METRIC_PLAYER_IMPRESSION_RATIO, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION);
            self::$aggregations_def[self::METRIC_PLAYTHROUGH_RATIO] = self::getRatioAggTemplate(self::METRIC_PLAYTHROUGH_RATIO, self::EVENT_TYPE_PLAYTHROUGH_100, self::EVENT_TYPE_PLAY);
            
            $play_time_aggr = self::$event_type_count_aggr_template;
            $play_time_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = self::EVENT_TYPE_PLAY_END;
            $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_FIELD_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            
            $play_through_aggr = self::$events_types_count_aggr_template;
            $play_through_aggr[self::DRUID_FILTER][self::DRUID_TYPE] = self::DRUID_IN_FILTER;
            $play_through_aggr[self::DRUID_FILTER][self::DRUID_VALUES] = array(self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100);
            $play_through_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_PLAYTHROUGH;
            
            $total_entries_aggr = array(self::DRUID_TYPE => self::DRUID_CARDINALITY,
                self::DRUID_NAME => self::METRIC_TOTAL_ENTRIES,
                self::DRUID_FIELDS => array(self::DIMENSION_ENTRY_ID));
            
            $unique_users_aggr = array(self::DRUID_TYPE => self::DRUID_HYPER_UNIQUE,
                self::DRUID_NAME => self::METRIC_UNIQUE_USERS,
                self::DRUID_FIELD_NAME => self::METRIC_UNIQUE_USER_IDS);
            
            self::$aggregations_def[self::METRIC_TOTAL_PLAY_TIME] = $play_time_aggr;
            self::$aggregations_def[self::METRIC_PLAYTHROUGH] = $play_through_aggr;
            self::$aggregations_def[self::METRIC_TOTAL_ENTRIES] = $total_entries_aggr;
            self::$aggregations_def[self::METRIC_UNIQUE_USERS] = $unique_users_aggr;
            
            
            $avg_dropoff_ratio = self::$arithmetic_post_aggr_template;
            $avg_dropoff_ratio[self::DRUID_NAME] = self::METRIC_AVG_DROP_OFF;
            $avg_dropoff_ratio[self::DRUID_FUNCTION] = "/";
            
            $avg_dropoff_ratio_sub_calc = self::$arithmetic_post_aggr_template;
            $avg_dropoff_ratio_sub_calc[self::DRUID_FUNCTION] = "/";
            $avg_dropoff_ratio_sub_calc[self::DRUID_NAME] = "subDropOff";
            $sub_calc_fields = array(array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => self::METRIC_PLAYTHROUGH, self::DRUID_FIELD_NAME => self::METRIC_PLAYTHROUGH),
                array(self::DRUID_TYPE => self::DRUID_CONSTANT, self::DRUID_NAME => "quater", "value" => "4"));
            $avg_dropoff_ratio_sub_calc[self::DRUID_FIELDS] = $sub_calc_fields;
            $avg_dropoff_ratio[self::DRUID_FIELDS] = array($avg_dropoff_ratio_sub_calc, array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => self::EVENT_TYPE_PLAY, self::DRUID_FIELD_NAME => self::EVENT_TYPE_PLAY));
            
            self::$aggregations_def[self::METRIC_AVG_DROP_OFF] = $avg_dropoff_ratio;
            
            foreach (self::$metricsToHeaders as $metric => $header)
            {
                self::$headersToMetrics[$header] = $metric;
            }
        }    
    }
    
    public static function getGraph ( $partner_id , $report_type , reportsInputFilter $input_filter , $dimension = null , $object_ids = null)
    {
        self::init();
        $start = microtime(true);
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_GRAPH , $input_filter , null , null , null , $object_ids, $input_filter->timeZoneOffset );
        $report_str = myReportsMgr::$type_map[$report_type];
        switch ($report_type)
        {
            case myReportsMgr::REPORT_TYPE_PLATFORMS:
                if ($object_ids != NULL && count($object_ids) > 0)
                    $res = self::getMultiGraphsByColumnName( $result , $report_str, self::DIMENSION_OS);
                else
                    $res = self::getMultiGraphsByDateId ( $result , self::DIMENSION_DEVICE, $report_str, $input_filter->timeZoneOffset);
                break;
            case myReportsMgr::REPORT_TYPE_OPERATION_SYSTEM:
            case myReportsMgr::REPORT_TYPE_BROWSERS:
                $dimension = self::$reportsDimensions[$report_str];
                $res = self::getMultiGraphsByColumnName( $result , $report_str, $dimension);     
                break;
            case myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF:
            case myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF:
                $res = self::getGraphsByColumnName ( $result , $report_str);
                break;
            default:
                $res = self::getGraphsByDateId ( $result , $report_str, $input_filter->timeZoneOffset);    
        }
               
        $end = microtime(true);
        KalturaLog::log( "getGraph took [" . ( $end - $start ) . "]" );
        
        return $res;
    }
    
    public static function getTotal ( $partner_id , $report_type , reportsInputFilter $input_filter , $object_ids = null  )
    {
        self::init();
        $start = microtime ( true );
        
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_TOTAL , $input_filter , null , null , null , $object_ids, $input_filter->timeZoneOffset);
        if ( count($result) > 0 )
        {
            $row = $result[0];
            $row_data = $row->result;
            $header = array();
            $type_str = myReportsMgr::$type_map[$report_type];
            $total_metrics = self::$reportsMetrics[$type_str];
            if (array_key_exists($type_str, self::$reportTotalAdditionalMetrics))
                $total_metrics= array_merge(self::$reportTotalAdditionalMetrics[$type_str], $total_metrics); 
            foreach ( $total_metrics as $column )
            {
                $headers[] = self::$metricsToHeaders[$column];
                $data[] = $row_data->$column;
            }
            
            foreach (self::$transform_metrics as $metric) {
                $field_index = array_search(self::$metricsToHeaders[$metric], $headers);
                if (false !== $field_index) {
                    $data[$field_index] = floor($data[$field_index]);
                }
            }
            
            $res = array ( $headers, $data );           
        }
        else
        {
            //			return $result[0]; // for total - there is only a single record
            $res = array ( null , null );
        }
        
        $end = microtime(true);
        KalturaLog::log( "getTotal took [" . ( $end - $start ) . "]" );
        
        return $res;
    }
    
    
    public static function getTable ( $partner_id , $report_type , reportsInputFilter $input_filter  ,
        $page_size , $page_index , $order_by , $object_ids = null)
    {
        self::init();
        $start = microtime ( true );
        $total_count = 0;
        
        if ( ! $page_size || $page_size < 0 ) $page_size = 10;
        //$page_size = min($page_size , self::REPORTS_TABLE_MAX_QUERY_SIZE);
        
        if ( ! $page_index || $page_index < 0 ) $page_index = 0;
        if ($page_index * $page_size > 12000) 
        {
            //todo: result is too big
        }
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_TABLE , $input_filter ,$page_size , $page_index , $order_by , $object_ids, $input_filter->timeZoneOffset );
        if ( count($result) > 0 )
        {
            $report_str = myReportsMgr::$type_map[$report_type];
            $rows = $result[0]->result;
            $rows_count = count($rows);
            
            if ($page_index * $page_size > $rows_count)
            {
                $total_count = $rows_count;
            }
            
            if ($page_index * $page_size < $rows_count) {
                $rows = array_slice($rows, ($page_index -1) * $page_size, $page_size);
            }
            if ($page_index * $page_size >= $rows_count) {
                if (($page_index -1) * $page_size < $rows_count)
                    $rows = array_slice($rows, ($page_index -1) * $page_size);
                else 
                    $rows = array();
            }
                
            if ($rows)
            {
                $dimensionIds = array();
                $headers = array();
                
                $dimensionHeaders = self::$detailDimensionHeaders[$report_str];
                $reportMetrics = ($report_type == myReportsMgr::REPORT_TYPE_APPLICATIONS) ? array() : self::$reportsMetrics[$report_str];
                $dimension = self::$reportsDimensions[$report_str];
                if ($object_ids)
                {
                    if (array_key_exists($report_str."_drilldown", self::$detailDimensionHeaders))
                        $dimensionHeaders= self::$detailDimensionHeaders[$report_str. "_drilldown"];
                    if (array_key_exists($report_str."_drilldown", self::$reportsDimensions))
                        $dimension = self::$reportsDimensions[$report_str. "_drilldown"];
                }
                foreach ($dimensionHeaders as $header) {
                    $headers[] = $header;
                }
                foreach ( $reportMetrics as $column )
                {
                    $headers[] = self::$metricsToHeaders[$column];
                }
                
                $data = array();
                
                
                
                
                foreach ( $rows as $row )
                {
                    $dimensionIds[] = $row->$dimension;
                    $rowData = array();
                    foreach ($dimensionHeaders as $header) {
                        $rowData[] = $row->$dimension;
                    }
                    
                    foreach ( $reportMetrics as $column )
                    {
                        $rowData[] = $row->$column;
                    }
                    $data[] = $rowData;
                    
                }
                
                foreach (self::$transform_metrics as $metric) {
                    $field_index = array_search(self::$metricsToHeaders[$metric], $headers);
                    if (false !== $field_index) {
                        $rows_count = count($data);
                        for ($i = 0; $i < $rows_count; $i++) {
                            $data[$i][$field_index] = floor($data[$i][$field_index]);
                        }
                    }
                }
                
                if (array_key_exists($report_str, self::$reportsToEnrich)) {
                    $enrich_func = self::$reportEnrichFunc[$report_str];
                    $entities = call_user_func($enrich_func, $dimensionIds, $partner_id);
                    $enrich_field = array_search(self::$reportsToEnrich[$report_str], $headers);
                    if (!$enrich_field) {
                        $enrich_field = 0;
                    }
                    
                    $rows_count = count($data);
                    for ($i = 0; $i < $rows_count; $i++) {
                        $data[$i][$enrich_field] = $entities[$data[$i][$enrich_field]];
                    }
                    
                }
                
                // since the query limit is $page_index * page_size we can set result count as total count in case limit is bigger than result count 
                // and don't count the cardinality query
                if ($total_count == 0) 
                {
                    if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, myReportsMgr::$end_user_filter_get_count_reports) )
                    {
                        $total_count = self::getTotalTableCount( $partner_id , $report_type , $input_filter  ,
                            $page_size , $page_index , $order_by , $object_ids );
                        
                        if ( $total_count <= 0 )
                        {
                            $end = microtime(true);
                            KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );
                            return array ( array() , array() , 0 );
                        }
                    }
                    
                }
                $res = array ( $headers , $data , $total_count );
            }
            else
            {
                $res =  array ( array() , array() , $total_count);
            }
        }
        
        else
        {
            $res =  array ( array() , array() , 0 );
        }
        
        $end = microtime(true);
        KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );
        
        return $res;
        
        
    }
    
    private static function getGranularityDef($granularity, $timezone_offset) 
    {
       $granularity_def = self::DRUID_GRANULARITY_ALL;
       $timezone_name = timezone_name_from_abbr("", $timezone_offset * 60 * -1, 0);
       if ($granularity === self::DRUID_GRANULARITY_DAY) {
           $granularity_def = array(self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
                                    self::DRUID_GRANULARITY_PERIOD => "P1D",
                                    self::DRUID_TIMEZONE => $timezone_name);
       }
       if ($granularity === self::DRUID_GRANULARITY_HOUR) {
           $granularity_def = array(self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
               self::DRUID_GRANULARITY_PERIOD => "PT1H",
               self::DRUID_TIMEZONE => $timezone_name);
       }
       return  $granularity_def;
    }
    
    public static function getReport($partner_id, $type_str , $report_flavor, $object_ids, $input_filter, $filter, $order_by, $order_dir, $threshold, $tz = 0) {
       
       $intervals = array(self::dateIdToInterval($input_filter->from_day, $tz) . "/" .  self::dateIdToInterval($input_filter->to_day, $tz, true));
       $dimension = "";
       if ($object_ids) 
           if (array_key_exists($type_str ."_drilldown", self::$reportsDimensions)) 
               $dimension = self::$reportsDimensions[$type_str . "_drilldown"];
       if (!$dimension)
           $dimension = self::$reportsDimensions[$type_str];
       $metrics = self::$reportsMetrics[$type_str];
       $query = "";
       switch ($report_flavor) {
           case myReportsMgr::REPORT_FLAVOR_GRAPH:
            $granularity = array_key_exists($type_str, self::$reportsGranularity) ? self::$reportsGranularity[$type_str] : self::DRUID_GRANULARITY_DAY;
            if ($object_ids)
                $granularity = array_key_exists($type_str."_drilldown", self::$reportsGranularity) ? self::$reportsGranularity[$type_str. "_drilldown"] : $granularity;
            
            $granularity = self::getGranularityDef($granularity, $tz);
                
            if ($type_str === myReportsMgr::$type_map[myReportsMgr::REPORT_TYPE_PLATFORMS] || $type_str === myReportsMgr::$type_map[myReportsMgr::REPORT_TYPE_OPERATION_SYSTEM] || $type_str === myReportsMgr::$type_map[myReportsMgr::REPORT_TYPE_BROWSERS])
                $query = self::getGroupByReport($partner_id, $intervals, $granularity, array($dimension), $metrics, $filter);
            else
                $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter);
            break;
           case myReportsMgr::REPORT_FLAVOR_TABLE:
            $granularity = self::DRUID_GRANULARITY_ALL;
            $query = self::getTopReport($partner_id, $intervals, $metrics, $dimension, $filter, $order_by, $order_dir, $threshold);
            break;
           case myReportsMgr::REPORT_FLAVOR_TOTAL:
            $granularity = self::DRUID_GRANULARITY_ALL;
            if (array_key_exists($type_str, self::$reportTotalAdditionalMetrics))
                $metrics = array_merge(self::$reportTotalAdditionalMetrics[$type_str], $metrics); 
            $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter);
            break;
           case myReportsMgr::REPORT_FLAVOR_COUNT:
            $query = self::getDimCardinalityReport($partner_id, $intervals, $dimension, $filter);
            break;
           
        
       }
       return $query;
   }
   
   private static function dateIdToInterval($dateId, $offset, $end_of_the_day = false) 
   {
       $year = substr($dateId, 0, 4);
       $month = substr($dateId, 4, 2);
       $day = substr($dateId, 6, 2);
       
       $timezone_offset_minutes = "00";
       if (($offset % 60)) {
           $timezone_offset_minutes = "30";
       }
       $timezone_offset = $offset/60*-1;
       if ($timezone_offset >= 0 && $timezone_offset < 10) {
           $timezone_offset = "+0$timezone_offset";
       } else if ($timezone_offset >= 10) {
           $timezone_offset = "+$timezone_offset";
       } else if ($timezone_offset < 0 && $timezone_offset > -10) {
           $timezone_offset = $timezone_offset * -1;           
           $timezone_offset = "-0$timezone_offset";
       }
       $time = $end_of_the_day? "T23:59:59" : "T00:00:00";
       
       return "$year-$month-$day$time$timezone_offset:$timezone_offset_minutes";
       
   }
   
   // shift date by tz offset
   private static function timestampToDateId($timestamp, $offset) 
   {
       $date = new DateTime($timestamp);
       $date->modify($offset*-1 . " minute");
       $client_timestamp = $date->format('Y-m-d H:i:s'); // 2012-07-15 05:00:00 
       
       $year = substr($client_timestamp, 0, 4);
       $month = substr($client_timestamp, 5, 2);
       $day = substr($client_timestamp, 8, 2);
       return "$year$month$day";
   }
   
   // hours are returned from druid query with the right offset so no need to change it
   private static function timestampToHourId($timestamp, $offset)
   {
       $year = substr($timestamp, 0, 4);
       $month = substr($timestamp, 5, 2);
       $day = substr($timestamp, 8, 2);
       $hour = substr($timestamp, 11, 2);
       return "$year$month$day$hour";
   }
   
   
   private static function executeQueryByType ( $partner_id , $report_type , $report_flavor , reportsInputFilter $input_filter  ,
       $page_size , $page_index , $order_by , $object_ids = null , $offset = 0)
   {
       $start = microtime(true);
       try
       {
        
           $entryFilter = new entryFilter();
           $entryFilter->setPartnerSearchScope($partner_id);
           $shouldSelectFromSearchEngine = false;
           
           $druid_filter = null;
           if ($report_type == myReportsMgr::REPORT_TYPE_LIVE) 
           {
               $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE,
                   self::DRUID_VALUES => array("live"));
           } 
           else
           {
               $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE,
                   self::DRUID_VALUES => array("vod"));
           }
           if ($input_filter instanceof endUserReportsInputFilter)
           {
               if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
               {
                   $categoryFilter = new categoryFilter();
                   if ($input_filter->playbackContext && $input_filter->ancestorPlaybackContext)
                       $categoryIds = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
                   else 
                   {
                           if ($input_filter->playbackContext)
                               $categoryFilter->set("_in_full_name", $input_filter->playbackContext);
                           if ($input_filter->ancestorPlaybackContext)
                               $categoryFilter->set("_matchor_likex_full_name", $input_filter->ancestorPlaybackContext);
                                   
                           $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
                           $categoryFilter->attachToCriteria($c);
                           $categoryFilter->setPartnerSearchScope($partner_id);
                           $c->applyFilters();
                                   
                           $categoryIdsFromDB = $c->getFetchedIds();
                                   
                           if (count($categoryIdsFromDB))
                                $categoryIds = implode(",", $categoryIdsFromDB);
                           else
                                $categoryIds = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
                   }
                   $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
                       self::DRUID_VALUES => explode(',', $categoryIds)
                   );
               }
               
               if ($input_filter->application) {
                   $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_APPLICATION,
                       self::DRUID_VALUES => explode(',', $input_filter->application)
                   );
               }
               if ($input_filter->userIds != null) {
                   $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_USER_ID,
                       self::DRUID_VALUES => explode(",", $input_filter->userIds)
                   );
               }
           }
           
           if ($input_filter->categories)
           {
               $categoryIds = self::getCategoriesIds($input_filter->categories, $partner_id);
               $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES,
                   self::DRUID_VALUES => $categoryIds
               );
           }
           
           if ($input_filter->keywords)
           {
               if($input_filter->search_in_tags)
                   $entryFilter->set("_free_text", $input_filter->keywords);
               else
                   $entryFilter->set("_like_admin_tags", $input_filter->keywords);
                   $shouldSelectFromSearchEngine = true;
           }
           
           $entryIdsFromDB = array();
           
           if ($shouldSelectFromSearchEngine)
           {
               $c = KalturaCriteria::create(entryPeer::OM_CLASS);
               $entryFilter->attachToCriteria($c);
               $c->applyFilters();
               
               $entryIdsFromDB = $c->getFetchedIds();
               
               if ($c->getRecordsCount() > count($entryIdsFromDB))
                   throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL );
                   
                   if (!count($entryIdsFromDB))
                       $entryIdsFromDB[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;
           }
           
           $type_str = myReportsMgr::$type_map[$report_type];
           
           if($object_ids)
           {
               $object_ids_arr = explode(",", $object_ids);
               
               switch ($report_type)
               {
                   case myReportsMgr::REPORT_TYPE_TOP_SYNDICATION:
                   case myReportsMgr::REPORT_TYPE_MAP_OVERLAY:
                   case $report_type == myReportsMgr::REPORT_TYPE_PLATFORMS:
                       $druid_filter[] = array(self::DRUID_DIMENSION => self::$reportsDimensions[$type_str],
                                               self::DRUID_VALUES => $object_ids_arr
                       );
                   break;    
                   default:
                       $entryIds = array_merge($object_ids_arr, $entryIdsFromDB);
                       
                       $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_ENTRY_ID,
                           self::DRUID_VALUES => $entryIds
                       );
                       
               }
           }
           elseif (count($entryIdsFromDB))
           {
               $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_ENTRY_ID,
                   self::DRUID_VALUES => $entryIdsFromDB
               );
           }
           
           $order_by_dir = "-";
           if (!$order_by) {
               $order_by = self::$reportsMetrics[$type_str][0];
               $order_by_dir = "-";
           }
           else 
           {
               if ($order_by[0] === "-" || $order_by[0] === "+") {
                   $order_by_dir = substr($order_by, 0, 1);
                   $order_by = substr($order_by, 1);
               }
                   
               $order_by = self::$headersToMetrics[$order_by];
           }
           
           $query = self::getReport($partner_id, $type_str, $report_flavor, $object_ids, $input_filter, $druid_filter, $order_by, $order_by_dir, $page_size * $page_index, $offset);
                       
           $query_header = "/* -- " .$type_str . " " . myReportsMgr::$flavor_map[$report_flavor] . " -- */\n";
           $json_query = json_encode($query);
           KalturaLog::log( "\n{$query_header}{$json_query}" );
                               
           $res = self::runReport($query);
                                       
           $end = microtime(true);
           KalturaLog::log( "Query took [" . ( $end - $start ) . "]" );
           return $res;
       }
       catch ( Exception $ex )
       {
           KalturaLog::log( $ex->getMessage() );
           // TODO - write proeper error
           if ($ex->getCode() == kCoreException::SEARCH_TOO_GENERAL);
           throw $ex;
           
           throw new Exception ( "Error while processing report for [$partner_id , $report_type , $report_flavor]" );
       }
   }
   
   
   private static function getTopReport($partner_id, $intervals, $metrics, $dimensions, $filter, $order_by, $order_dir, $page_size = 10) 
   {
       $report_def = self::$top_n_query_template;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_DIMENSION] = $dimensions;
       $order_type = self::DRUID_NUMERIC;
       if ($order_dir === "+")
           $order_type = self::DRUID_INVERTED;
       $report_def[self::DRUID_METRIC] = array(self::DRUID_TYPE => $order_type,
                                               self::DRUID_METRIC => $order_by);
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
                                                   self::DRUID_FIELDS => $filter_def);
           
       } else { 
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       } 
       
       $report_def[self::DRUID_THRESHOLD] = $page_size;
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       foreach ($metrics as $metric ) 
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR])))
                       $report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
               }
               if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr)) 
               {
                   foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr) {
                       $report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
                   }
               }
           }
       }
       return $report_def;
   }
   
   private static function buildFilter($filters) {
       $filters_def = array();
       foreach ($filters as $filter) {
           if (sizeof( $filter[self::DRUID_VALUES]) == 1)
                $filters_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER,
                                       self::DRUID_DIMENSION => $filter[self::DRUID_DIMENSION],
                                       self::DRUID_VALUE => $filter[self::DRUID_VALUES][0]
                );
                
           else 
               $filters_def[] = array(self::DRUID_TYPE => self::DRUID_IN_FILTER,
                   self::DRUID_DIMENSION => $filter[self::DRUID_DIMENSION],
                   self::DRUID_VALUES => $filter[self::DRUID_VALUES]
               );
       }
       return $filters_def;
   }
   
   private static function getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter)
   {
       $report_def = self::$time_series_query_teample;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = $granularity;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
               self::DRUID_FIELDS => $filter_def);
           
       } else {
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       }
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       foreach ($metrics as $metric )
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR])))
                       $report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
               }
               if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr))
                   foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr) {
                       $report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
                    }
               
           }
       }
       return $report_def;
   }
   
   private static function getDimCardinalityReport($partner_id, $intervals, $dimension, $filter)
   {
       $report_def = self::$time_series_query_teample;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = self::DRUID_GRANULARITY_ALL;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
       }
       $filter_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       $filter_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE, self::DRUID_VALUE => self::EVENT_TYPE_PLAYER_IMPRESSION);
       $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
               self::DRUID_FIELDS => $filter_def);
           
       
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       $report_def[self::DRUID_AGGR][] = array(self::DRUID_TYPE => self::DRUID_CARDINALITY,
                                               self::DRUID_NAME => "total_count",
                                               self::DRUID_FIELDS => array($dimension));
      
       return $report_def;
   }
   
   private static function getGroupByReport($partner_id, $intervals, $granularity, $dimensions, $metrics, $filter, $pageSize = 0)
   {
       $report_def = self::$group_by_query_template;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = $granularity;
       $report_def[self::DRUID_DIMENSIONS] = $dimensions;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
               self::DRUID_FIELDS => $filter_def);
           
       } else {
           $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       }
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       foreach ($metrics as $metric )
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR])))
                       $report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
               }
               if ($metric_aggr[self::DRUID_POST_AGGR])
               {
                   foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr) {
                       $report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
                    }
               }
               
           }
       }
       return $report_def;
   }
   
   public static function runReport($content) {
       $post = json_encode($content);
       
       $remote_path = kConf::get('druid_url');
       //"http://52.42.180.203:8082/druid/v2/";
       
       
       
       $ch = curl_init($remote_path);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
       curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       $results = curl_exec($ch);
       curl_close($ch);
       
       $json_res = json_decode($results);
       return $json_res;
   }
   
   public static function getGraphsByDateId ( $result , $report_type, $tz_offset )
   {
       $graphs = array();
       $graphMetrics = self::$reportsGraphMetrics[$report_type];
       
       foreach ( $graphMetrics as $column )
       {
           $graphs[self::$metricsToHeaders[$column]] = array();
       }
       
       foreach ( $result as $row )
       {
           $row_data = $row->result;
           
           if ($report_type === "live")
                $date = self::timestampToHourId($row->timestamp, $tz_offset);
           else
               $date = self::timestampToDateId($row->timestamp, $tz_offset);
         
           foreach ( $graphMetrics as $column )
           {
               $graph = $graphs[self::$metricsToHeaders[$column]];
               $graph[$date] = $row_data->$column; // the value for graph 1 will be column #1 in the row
               $graphs[self::$metricsToHeaders[$column]] = $graph;
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByDateId ( $result , $multiline_column, $report_type, $tz_offset )
   {
       $graphs = array();
       $graphMetrics = self::$reportsGraphMetrics[$report_type];
       
       foreach ( $graphMetrics as $column )
       {
           if ($column != $multiline_column)
                $graphs[self::$metricsToHeaders[$column]] = array();
       }
       
       foreach ( $result as $row )
       {
           $row_data = $row->event;
           
           $date = self::timestampToDateId($row->timestamp, $tz_offset);
           $multiline_val = $row_data->$multiline_column;
           foreach ( $graphMetrics as $column )
           {
               if ($column != $multiline_column)
               {   
                   $graph = $graphs[self::$metricsToHeaders[$column]];
                   if ($graph[$date] != null)
                       $graph[$date] =  $graph[$date] . "," . $multiline_val . ":" .  $row_data->$column; // the value for graph 1 will be column #1 in the row
                       else
                           $graph[$date] = $multiline_val . ":" .  $row_data->$column;
                           $graphs[self::$metricsToHeaders[$column]] = $graph;
               }
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByColumnName ( $result , $report_type, $dimension )
   {
       $graphs = array();
       $graphMetrics = self::$reportsGraphMetrics[$report_type];
       
       foreach ( $graphMetrics as $column )
       {
           $graphs[self::$metricsToHeaders[$column]] = array();
       }
       
       foreach ( $result as $row )
       {
           $row_data = $row->event;
           
           $dimValue = $row_data->$dimension;
           
           foreach ( $graphMetrics as $column )
           {
               $graph = $graphs[self::$metricsToHeaders[$column]];
               $graph[$dimValue] = $row_data->$column; // the value for graph 1 will be column #1 in the row
               $graphs[self::$metricsToHeaders[$column]] = $graph;
           }
       }
       return $graphs;
   }
   
   public static function getGraphsByColumnName ( $result , $type_str)
   {
       $graphs = array();
       $graphMetrics = self::$reportsGraphMetrics[$type_str];
       
       foreach ( $result as $row )
       {
           $row_data = $row->result;
           foreach ( $graphMetrics as $column )
           {
               $graph[self::$metricsToHeaders[$column]] = $row_data->$column;
           }
       }
       
       $graphs[$type_str] = $graph;
       return $graphs;
   }
   
   
   private static function getEntriesNames($ids, $partner_id)
   {
       $entryFilter = new entryFilter();
       $entryFilter->setPartnerSearchScope($partner_id);
       $entryFilter->setIdIn($ids);
       $c = KalturaCriteria::create(entryPeer::OM_CLASS);
       $entryFilter->attachToCriteria($c);
       $c->applyFilters();
       
       $entries = entryPeer::doSelect( $c );
       
       if( ! $entries) return null;
       $entriesNames = array ();
       foreach ( $entries  as $entry )
       {
           $entriesNames[$entry->getId()] = $entry->getName();
       }
       return $entriesNames;

       
   }

   private static function getCategoriesNames($ids, $partner_id)
   {
       $categoryFilter= new categoryFilter();
       $categoryFilter->setPartnerSearchScope($partner_id);
       $categoryFilter->setIdIn($ids);
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $categoryFilter->attachToCriteria($c);
       $c->applyFilters();
       
       $categories = entryPeer::doSelect( $c );
       
       if( !$categories) return null;
       $categoriesNames = array ();
       foreach ( $categories as $category )
       {
           $categoriesNames[$category->getId()] = $category->getName();
       }
       return $categoriesNames;
       
       
   }
   
   private static function getCategoriesIds($categories, $partner_id)
   {
       $categoryFilter = new categoryFilter();
       $categoryFilter->set("_eq_full_name", $categories);
       $categoryFilter->setPartnerSearchScope($partner_id);
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $categoryFilter->attachToCriteria($c);
       $c->applyFilters();
                       
       $categoryIdsFromDB = $c->getFetchedIds();
                       
       if (count($categoryIdsFromDB))
       {
            $categoryIds = $categoryIdsFromDB;
       }
       else
       {
            $categoryIds = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
       }
       return $categoryIds;
       
   }
   
   
   private static function getTotalTableCount($partner_id, $report_type, reportsInputFilter $input_filter, $page_size, $page_index, $order_by, $object_ids = null)
   {
       
       $cache_key = self::createCacheKey ($partner_id, $report_type, $input_filter, $object_ids );
     
       $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_REPORTS_COUNT);
       
       $total_count = $cache->get($cache_key);
       if ($total_count)
       {
           KalturaLog::log("count from cache: [$total_count]");
           return $total_count;
       }
       
       $total_count_arr = self::executeQueryByType($partner_id, $report_type, myReportsMgr::REPORT_FLAVOR_COUNT, $input_filter, null, null, null, $object_ids);
       if ($total_count_arr && isset ($total_count_arr[0]->result->total_count))
       {
           $total_count = floor($total_count_arr[0]->result->total_count);
       }
       else
       {
           $total_count = 0;
       }
       KalturaLog::log("count: [$total_count]");
       
       
       $cache->set($cache_key, $total_count, myReportsMgr::REPORTS_COUNT_CACHE); // store in the cache for next time
       return $total_count;
   }
   
   private static function createCacheKey ( $partner_id , $report_type , reportsInputFilter  $input_filter , $object_ids )
   {
       if (strlen( $partner_id ) > 40)
           $partner_id_str = md5($partner_id);
       else
           $partner_id_str = $partner_id;
               
       $key = 'reportCount-'.md5("$partner_id|$report_type|$object_ids|".serialize($input_filter));
       return $key;
   }
}
