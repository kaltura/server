<?php

class kKavaReportsMgr
{
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
 
    const REPORT_DIMENSIONS = "report_dimensions";
    const REPORT_METRICS = "report_metrics";
    const REPORT_DETAIL_DIM_HEADERS = "report_deatil_dimensions_headers";
    const REPORT_GRAPH_METRICS = "report_graph_metrics";
    const REPORT_ENRICH_DEF = "report_enrich_definition";
    const REPORT_GRANULARITY = "report_granularity";
    const REPORT_ENRICH_FIELD = "report_enrich_field";
    const REPORT_ENRICH_FUNC = "report_enrich_func";
    const REPORT_TOTAL_ADDITIONAL_METRICS = "report_total_metrics";
    const REPORT_DRILLDOWN_GRANULARITY = "report_drilldown_granularity";
    const REPORT_DRILLDOWN_DIMENSIONS = "report_drilldown_dimensions";
    const REPORT_DRILLDOWN_METRICS = "report_drilldown_metrics";
    const REPORT_DRILLDOWN_DETAIL_DIM_HEADERS = "report_drilldown_deatil_dimensions_headers";
    const REPORT_CARDINALITY_METRIC = "report_caredinality_metric";
    
    static $aggregations_def = array();
    static $metrics_def = array();
    
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
              self::DRUID_DIMENSIONS => "dimension");
    
    private static $simple_metrics = array(
        self::EVENT_TYPE_PLAYER_IMPRESSION,
        self::EVENT_TYPE_PLAY_REQUESTED,
        self::EVENT_TYPE_PLAY,
        self::EVENT_TYPE_RESUME,
        self::EVENT_TYPE_PLAYTHROUGH_25,
        self::EVENT_TYPE_PLAYTHROUGH_50,
        self::EVENT_TYPE_PLAYTHROUGH_75,
        self::EVENT_TYPE_PLAYTHROUGH_100,
        self::METRIC_PLAYTHROUGH,
        self::EVENT_TYPE_EDIT_CLICKED,
        self::EVENT_TYPE_SHARE_CLICKED,
        self::EVENT_TYPE_SHARED,
        self::EVENT_TYPE_DOWNLOAD_CLICKED,
        self::EVENT_TYPE_REPORT_CLICKED,
        self::EVENT_TYPE_REPORT_SUBMITTED,
        self::EVENT_TYPE_ENTER_FULL_SCREEN,
        self::EVENT_TYPE_EXIT_FULL_SCREEN,
        self::EVENT_TYPE_PAUSE,
        self::EVENT_TYPE_REPLAY,
        self::EVENT_TYPE_SEEK,
        self::EVENT_TYPE_RELATED_CLICKED,
        self::EVENT_TYPE_RELATED_SELECTED,
        self::EVENT_TYPE_CAPTIONS,
        self::EVENT_TYPE_SOURCE_SELECTED,
        self::EVENT_TYPE_INFO,
        self::EVENT_TYPE_SPEED,
        self::EVENT_TYPE_VIEW,
        self::METRIC_TOTAL_ENTRIES,
        self::METRIC_TOTAL_PLAY_TIME,
        self::EVENT_TYPE_PLAY_END,
        self::METRIC_UNIQUE_USERS);
    
    private static $simple_event_types = array(
        self::EVENT_TYPE_PLAY,
        self::EVENT_TYPE_PLAYER_IMPRESSION,
        self::EVENT_TYPE_PLAY_END,
        self::EVENT_TYPE_PLAYTHROUGH_25,
        self::EVENT_TYPE_PLAYTHROUGH_50,
        self::EVENT_TYPE_PLAYTHROUGH_75,
        self::EVENT_TYPE_PLAYTHROUGH_100,
        self::EVENT_TYPE_EDIT_CLICKED,
        self::EVENT_TYPE_SHARE_CLICKED,
        self::EVENT_TYPE_DOWNLOAD_CLICKED,
        self::EVENT_TYPE_REPORT_CLICKED
    );
    
    static $reportsDef = array(
        myReportsMgr::REPORT_TYPE_TOP_CONTENT => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")   
        ),
        myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames"),
            self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
        ),
        myReportsMgr::REPORT_TYPE_CONTENT_INTERACTIONS => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")
        ),
        myReportsMgr::REPORT_TYPE_MAP_OVERLAY => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_LOCATION_COUNTRY,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "country"),
            self::REPORT_DRILLDOWN_DIMENSIONS => self::DIMENSION_LOCATION_CITY,
            self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),           
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("object_id","location")
        ),
        myReportsMgr::REPORT_TYPE_TOP_SYNDICATION => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_DOMAIN,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id","domain_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_DIMENSIONS => self::DIMENSION_URL,
            self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("referrer")
        ),
        myReportsMgr::REPORT_TYPE_USER_ENGAGEMENT => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME ,self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),            
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF),    
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
        ),
        myReportsMgr::REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames"),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
        ),
        myReportsMgr::REPORT_TYPE_USER_TOP_CONTENT => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS =>  array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
            
        ),
        myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS),
            self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
            
        ),
        myReportsMgr::REPORT_TYPE_USER_CONTENT_INTERACTIONS => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
            
        ),
        myReportsMgr::REPORT_TYPE_APPLICATIONS => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_APPLICATION,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array("application"),
        ),
        myReportsMgr::REPORT_TYPE_PLATFORMS => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_DEVICE,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_DETAIL_DIM_HEADERS => array("device"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DRILLDOWN_DIMENSIONS => self::DIMENSION_OS,
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("os")
        ),
        myReportsMgr::REPORT_TYPE_OPERATION_SYSTEM => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_OS,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("os"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_DIMENSIONS => self::DIMENSION_BROWSER,
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("browser")
        ),
        myReportsMgr::REPORT_TYPE_BROWSERS => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_BROWSER,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("browser"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        
        ),
        myReportsMgr::REPORT_TYPE_LIVE => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_HOUR,
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")
        ),
        
        myReportsMgr::REPORT_TYPE_TOP_PLAYBACK_CONTEXT => array(
            self::REPORT_DIMENSIONS => self::DIMENSION_PLAYBACK_CONTEXT,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self:: METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "name", self::REPORT_ENRICH_FUNC => "self::getCategoriesNames")
        )
    );
     
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
    
    static $transform_metrics = array(self::METRIC_TOTAL_ENTRIES, self::METRIC_UNIQUE_USERS);
    
    private static function getCountAggrTemplate($event_type) {
        $count_aggr = self::$event_type_count_aggr_template;
        $count_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = $event_type;
        $count_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = $event_type;
        
        return $count_aggr;
    }
    
    private static function getRatioAggrTemplate($agg_name, $field1, $field2) 
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
            foreach (self::$simple_metrics as $metric) {
                self::$metrics_def[$metric] = array(self::DRUID_AGGR => array($metric));
            }
            
            self::$metrics_def[self::METRIC_AVG_PLAY_TIME] = array(self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME, self::EVENT_TYPE_PLAY),
                self::DRUID_POST_AGGR => array(self::METRIC_AVG_PLAY_TIME));
            self::$metrics_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
                self::DRUID_POST_AGGR => array(self::METRIC_PLAYER_IMPRESSION_RATIO));
            self::$metrics_def[self::METRIC_AVG_DROP_OFF] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYTHROUGH),
                self::DRUID_POST_AGGR => array(self::METRIC_AVG_DROP_OFF));
            self::$metrics_def[self::METRIC_PLAYTHROUGH_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_100),
                self::DRUID_POST_AGGR => array(self::METRIC_PLAYTHROUGH_RATIO));
            
            foreach (self::$simple_event_types as $event_type) {
                self::$aggregations_def[$event_type] = self::getCountAggrTemplate($event_type);
            }
            
            self::$aggregations_def[self::METRIC_AVG_PLAY_TIME] = self::getRatioAggrTemplate(self::METRIC_AVG_PLAY_TIME, self::METRIC_TOTAL_PLAY_TIME, self::EVENT_TYPE_PLAY);
            self::$aggregations_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = self::getRatioAggrTemplate(self::METRIC_PLAYER_IMPRESSION_RATIO, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION);
            self::$aggregations_def[self::METRIC_PLAYTHROUGH_RATIO] = self::getRatioAggrTemplate(self::METRIC_PLAYTHROUGH_RATIO, self::EVENT_TYPE_PLAYTHROUGH_100, self::EVENT_TYPE_PLAY);
            
            $play_time_aggr = self::$event_type_count_aggr_template;
            $play_time_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = self::EVENT_TYPE_PLAY_END;
            $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_FIELD_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            
            $play_time_aggr_new = self::$events_types_count_aggr_template;
            $play_time_aggr_new[self::DRUID_FILTER][self::DRUID_TYPE] = self::DRUID_IN_FILTER;
            $play_time_aggr_new[self::DRUID_FILTER][self::DRUID_VALUES] = array(self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100);
            $play_time_aggr_new[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            $play_time_aggr_new[self::DRUID_AGGREGATOR][self::DRUID_FIELD_NAME] = self::METRIC_TOTAL_PLAY_TIME;
            
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
                array(self::DRUID_TYPE => self::DRUID_CONSTANT, self::DRUID_NAME => "quarter", "value" => "4"));
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
        $intervals = self::getIntervals($input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimensions($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        $granularity = array_key_exists(self::REPORT_GRANULARITY, self::$reportsDef[$report_type]) ? self::$reportsDef[$report_type][self::REPORT_GRANULARITY] : self::DRUID_GRANULARITY_DAY;
        if ($object_ids)
            $granularity = array_key_exists(self::REPORT_DRILLDOWN_GRANULARITY, self::$reportsDef[$report_type]) ? self::$reportsDef[$report_type][self::REPORT_DRILLDOWN_GRANULARITY] : $granularity;
            
        $granularity = self::getGranularityDef($granularity, $input_filter->timeZoneOffset);
            
        if ($report_type == myReportsMgr::REPORT_TYPE_PLATFORMS || $report_type == myReportsMgr::REPORT_TYPE_OPERATION_SYSTEM || $report_type == myReportsMgr::REPORT_TYPE_BROWSERS)
            $query = self::getGroupByReport($partner_id, $intervals, $granularity, array($dimension), $metrics, $druid_filter);
        else
            $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $druid_filter);
        $result = self::runReport($query);
        
        $graphMetrics = self::$reportsDef[$report_type][self::REPORT_GRAPH_METRICS];
        foreach ($graphMetrics as $column)
        {
            $grepMetricsToHeaders[$column] = self::$metricsToHeaders[$column];
        }
        switch ($report_type)
        {
            case myReportsMgr::REPORT_TYPE_PLATFORMS:
                if ($object_ids != NULL && count($object_ids) > 0)
                    $res = self::getMultiGraphsByColumnName($result, $grepMetricsToHeaders, self::DIMENSION_OS);
                else
                    $res = self::getMultiGraphsByDateId ($result, self::DIMENSION_DEVICE, $grepMetricsToHeaders, $input_filter->timeZoneOffset);
                break;
            case myReportsMgr::REPORT_TYPE_OPERATION_SYSTEM:
            case myReportsMgr::REPORT_TYPE_BROWSERS:
                $dimension = self::$reportsDef[$report_type][self::REPORT_DIMENSIONS];
                $res = self::getMultiGraphsByColumnName($result, $grepMetricsToHeaders, $dimension);     
                break;
            case myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF:
            case myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF:
                $res = self::getGraphsByColumnName($result, $grepMetricsToHeaders, myReportsMgr::$type_map[$report_type]);
                break;
            default:
                $res = self::getGraphsByDateId ($result ,$grepMetricsToHeaders, $input_filter->timeZoneOffset, $report_type == myReportsMgr::REPORT_TYPE_LIVE);    
        }
               
        $end = microtime(true);
        KalturaLog::log( "getGraph took [" . ( $end - $start ) . "]" );
        
        return $res;
    }
    
    public static function getTotal ($partner_id ,$report_type ,reportsInputFilter $input_filter ,$object_ids = null)
    {
        self::init();
        $start = microtime ( true );
        $intervals = self::getIntervals($input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimensions($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        $granularity = self::DRUID_GRANULARITY_ALL;
        if (array_key_exists(self::REPORT_TOTAL_ADDITIONAL_METRICS, self::$reportsDef[$report_type]))
            $metrics = array_merge(self::$reportsDef[$report_type][self::REPORT_TOTAL_ADDITIONAL_METRICS], $metrics);
        $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $druid_filter);
        $result = self::runReport($query);    
        if ( count($result) > 0 )
        {
            $row = $result[0];
            $row_data = $row->result;
            $header = array();
            $total_metrics = self::$reportsDef[$report_type][self::REPORT_METRICS];
            if (array_key_exists(self::REPORT_TOTAL_ADDITIONAL_METRICS, self::$reportsDef[$report_type]))
                $total_metrics= array_merge(self::$reportsDef[$report_type][self::REPORT_TOTAL_ADDITIONAL_METRICS], $total_metrics); 
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
    
    
    public static function getTable($partner_id ,$report_type ,reportsInputFilter $input_filter,
        $page_size, $page_index, $order_by, $object_ids = null)
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
        $order_by_dir = "-";
        if (!$order_by) {
            $order_by = self::$reportsDef[$report_type][self::REPORT_METRICS][0];
            $order_by_dir = "-";
        }
        else
        {
            if ($order_by[0] === "-" || $order_by[0] === "+") {
                $order_by_dir = $order_by[0];
                $order_by = substr($order_by, 1);
            }
            
            if (isset(self::$headersToMetrics[$order_by]))
                $order_by = self::$headersToMetrics[$order_by];
        }
        
        $granularity = self::DRUID_GRANULARITY_ALL;
        $intervals = self::getIntervals($input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimensions($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        
        $query = self::getTopReport($partner_id, $intervals, $metrics, $dimension, $druid_filter, $order_by, $order_by_dir, $page_size * $page_index);
        $result = self::runReport($query);
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
                
                $dimensionHeaders = self::$reportsDef[$report_type][self::REPORT_DETAIL_DIM_HEADERS];
                $reportMetrics = ($report_type == myReportsMgr::REPORT_TYPE_APPLICATIONS) ? array() : self::$reportsDef[$report_type][self::REPORT_METRICS];
                $dimension = self::$reportsDef[$report_type][self::REPORT_DIMENSIONS];
                if ($object_ids)
                {
                    if (array_key_exists(self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS, self::$reportsDef[$report_type]))
                        $dimensionHeaders = self::$reportsDef[$report_type][self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS];
                        if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSIONS, self::$reportsDef[$report_type]))
                            $dimension = self::$reportsDef[$report_type][self::REPORT_DRILLDOWN_DIMENSIONS];
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
                
                if (array_key_exists(self::REPORT_ENRICH_DEF, self::$reportsDef[$report_type])) {
                    $enrich_func = self::$reportsDef[$report_type][self::REPORT_ENRICH_DEF][self::REPORT_ENRICH_FUNC];
                    $entities = call_user_func($enrich_func, $dimensionIds, $partner_id);
                    $enrich_field = array_search(self::$reportsDef[$report_type][self::REPORT_ENRICH_DEF][self::REPORT_ENRICH_FIELD], $headers);
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
                        $total_count = self::getTotalTableCount($partner_id ,$report_type ,$input_filter, $intervals, $druid_filter, $dimension, $object_ids);
                        
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
    
   private static function getIntervals($input_filter) {
       $input_filter->timeZoneOffset = round($input_filter->timeZoneOffset / 30) * 30;
       $intervals = array(self::dateIdToInterval($input_filter->from_day, $input_filter->timeZoneOffset) . "/" .  self::dateIdToInterval($input_filter->to_day, $input_filter->timeZoneOffset, true));
       return $intervals;  
   }
    
   private static function getDimensions($report_type, $object_ids) {
       $dimension = "";
       if ($object_ids)
           if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSIONS, self::$reportsDef[$report_type]))
               return self::$reportsDef[$report_type][self::REPORT_DRILLDOWN_DIMENSIONS];
       return self::$reportsDef[$report_type][self::REPORT_DIMENSIONS];        
   }
    
   private static function getMetrics($report_type) {
       return self::$reportsDef[$report_type][self::REPORT_METRICS];
   }
    
   private static function dateIdToInterval($dateId, $offset, $end_of_the_day = false) 
   {
       $year = substr($dateId, 0, 4);
       $month = substr($dateId, 4, 2);
       $day = substr($dateId, 6, 2);
       
       switch ($offset % 60) 
       {
            case 0: 
                $timezone_offset_minutes = "00";
                break;
            case 30:
                $timezone_offset_minutes = "30";
                break;
            default: 
                //throw exception;
       }
       
       $timezone_offset = -intval($offset/60);
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
       $client_timestamp = $date->format('Y-m-d');
       
       list($year, $month, $day) = explode("-", $client_timestamp);
       return "$year$month$day";
       
   }
   
   // hours are returned from druid query with the right offset so no need to change it
   private static function timestampToHourId($timestamp)
   {
       $date = new DateTime($timestamp);
       $client_timestamp = $date->format('Y-m-d-H');
       list($year, $month, $day, $hour) = explode("-", $client_timestamp);
       return "$year$month$day$hour";
   }
   
   private static function getDruidFilter($partner_id, $report_type, $input_filter, $object_ids) 
   {
       $entryFilter = new entryFilter();
       $entryFilter->setPartnerSearchScope($partner_id);
       
       $druid_filter = null;
       $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE,
           self::DRUID_VALUES => array($report_type == myReportsMgr::REPORT_TYPE_LIVE ? "live" : "vod"));
       
       if ($input_filter instanceof endUserReportsInputFilter)
       {
           if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
           {
               
               if ($input_filter->playbackContext && $input_filter->ancestorPlaybackContext)
                   $categoryIds = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
                   else
                       $categoryIds = self::getPlaybackContextCategoriesIds($partner_id, $input_filter->playbackContext ? $input_filter->playbackContext : $input_filter->ancestorPlaybackContext, isset($input_filter->ancestorPlaybackContext));
                       
                       $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
                           self::DRUID_VALUES => $categoryIds
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
       
       $entryIdsFromDB = array();
       if ($input_filter->keywords)
       {
           if($input_filter->search_in_tags)
               $entryFilter->set("_free_text", $input_filter->keywords);
           else
               $entryFilter->set("_like_admin_tags", $input_filter->keywords);
           
           $c = KalturaCriteria::create(entryPeer::OM_CLASS);
           $entryFilter->attachToCriteria($c);
           $c->applyFilters();
           
           $entryIdsFromDB = $c->getFetchedIds();
           
           if ($c->getRecordsCount() > count($entryIdsFromDB))
               throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL );
               
           if (!count($entryIdsFromDB))
               $entryIdsFromDB[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;
               
       }
       
       if($object_ids)
       {
           $object_ids_arr = explode(",", $object_ids);
           
           switch ($report_type)
           {
               case myReportsMgr::REPORT_TYPE_TOP_SYNDICATION:
               case myReportsMgr::REPORT_TYPE_MAP_OVERLAY:
               case myReportsMgr::REPORT_TYPE_PLATFORMS:
                   $druid_filter[] = array(self::DRUID_DIMENSION => self::$reportsDef[$report_type][self::REPORT_DIMENSIONS],
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
       
       return $druid_filter;
   } 
   
   private static function getBaseReportDef($partner_id, $intervals, $metrics, $filter, $granularity) {
       $report_def[self::DRUID_DATASOURCE] = self::HISTORICAL_DATASOURCE;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = $granularity;
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       foreach ($metrics as $metric )
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR]))) {
                       $report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
                       if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE]))
                           $event_types[] = self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE];
                           if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]))
                               $event_types = array_merge($event_types, self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]);
                   }
               }
               if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr))
               {
                   foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr) {
                       $report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
                   }
               }
           }
       }
       
       $filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
           self::DRUID_VALUES => $event_types
       );
       
       $filter_def = self::buildFilter($filter);
       $filter_def[]= array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
           self::DRUID_FIELDS => $filter_def);
       
       return $report_def;
   }
  
   private static function getTopReport($partner_id, $intervals, $metrics, $dimensions, $filter, $order_by, $order_dir, $page_size = 10) 
   {
       $report_def = self::getBaseReportDef($partner_id, $intervals, $metrics, $filter, self::DRUID_GRANULARITY_ALL);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TOPN;
       $report_def[self::DRUID_DIMENSION] = $dimensions;
       $order_type = self::DRUID_NUMERIC;
       if ($order_dir === "+")
           $order_type = self::DRUID_INVERTED;
       $report_def[self::DRUID_METRIC] = array(self::DRUID_TYPE => $order_type,
                                               self::DRUID_METRIC => $order_by);
       $report_def[self::DRUID_THRESHOLD] = $page_size;
             
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
       $report_def = self::getBaseReportDef($partner_id, $intervals, $metrics, $filter, $granularity);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;
       
       return $report_def;
   }
   
   private static function getDimCardinalityReport($partner_id, $intervals, $dimension, $filter, $event_type)
   { 
       $report_def = self::$time_series_query_teample;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = self::DRUID_GRANULARITY_ALL;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
       }
       
       $filter_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID, self::DRUID_VALUE => $partner_id);
       $filter_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER, self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE, self::DRUID_VALUE => $event_type);
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
       $report_def = self::getBaseReportDef($partner_id, $intervals, $metrics, $filter, $granularity);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_GROUP_BY;
       $report_def[self::DRUID_DIMENSIONS] = $dimensions;
       
       return $report_def;
   }
   
   public static function runReport($content) {
       
       KalturaLog::log( "{" . print_r($content, true) . "}" );
       
       $post = json_encode($content);
       KalturaLog::log($post);
       
       $remote_path = kConf::get('druid_url');       
       
       $ch = curl_init($remote_path);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
       curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       
       $druidStart = microtime(true);
       $results = curl_exec($ch);
       curl_close($ch);
       $druidTook = microtime(true) - $druidStart;
       KalturaLog::debug("Druid query took - " . $druidTook. " seconds");
       
       $json_res = json_decode($results);
       if (isset($json_res->error)) {
           throw new Exception("Error while running report $json_res->errorMessage");
       }
       
       return $json_res;
   }
   
   public static function getGraphsByDateId ($result, $grepMetricsToHeaders, $tz_offset, $isHourly = false)
   {
       $graphs = array();
       
       foreach ($grepMetricsToHeaders as $column => $header)
       {
           $graphs[$header] = array();
       }
       
       foreach ($result as $row)
       {
           $row_data = $row->result;
           
           if ($isHourly)
               $date = self::timestampToHourId($row->timestamp);
           else
               $date = self::timestampToDateId($row->timestamp, $tz_offset);
         
           foreach ($grepMetricsToHeaders as $column => $header)
           {
               $graphs[$header][$date] = $row_data->$column;
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByDateId ( $result , $multiline_column, $grepMetricsToHeaders, $tz_offset )
   {
       $graphs = array();
       
       foreach ( $grepMetricsToHeaders as $column => $header)
       {
           if ($column != $multiline_column)
                $graphs[$header] = array();
       }
       
       foreach ( $result as $row )
       {
           $row_data = $row->event;
           
           $date = self::timestampToDateId($row->timestamp, $tz_offset);
           $multiline_val = $row_data->$multiline_column;
           foreach ( $grepMetricsToHeaders as $column => $header )
           {
               if ($column == $multiline_column)
                   continue;
                  
               if (isset($graphs[$header][$date]))
                   $graphs[$header][$date] .=   ",";
               else 
                   $graphs[$header][$date] = "";
               
               $graphs[$header][$date] .= $multiline_val . ":" .  $row_data->$column;
               
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByColumnName ( $result , $grepMetricsToHeaders, $dimension )
   {
       $graphs = array();
       
       foreach ( $grepMetricsToHeaders as $column => $header )
       {
           $graphs[self::$metricsToHeaders[$column]] = array();
       }
       
       foreach ( $result as $row )
       {
           $row_data = $row->event;
           
           $dimValue = $row_data->$dimension;
           
           foreach ( $grepMetricsToHeaders as $column => $header )
           {
               $graphs[$header][$dimValue] = $row_data->$column;
           }
       }
       return $graphs;
   }
   
   public static function getGraphsByColumnName($result, $grepMetricsToHeaders, $type_str)
   {
       $graphs = array();
       
       foreach ($result as $row)
       {
           $row_data = $row->result;
           foreach ($grepMetricsToHeaders as $column => $header)
           {
               $graph[$header] = $row_data->$column;
           }
       }
       
       $graphs[$type_str] = $graph;
       return $graphs;
   }
   
   
   private static function getEntriesNames($ids, $partner_id)
   {
       $c = KalturaCriteria::create(entryPeer::OM_CLASS);
       $c->add(entryPeer::PARTNER_ID, $partner_id);
       $c->add(entryPeer::ID, $ids,Criteria::IN);
       
       entryPeer::setUseCriteriaFilter ( false );
       $entries = entryPeer::doSelect($c);
       entryPeer::setUseCriteriaFilter ( true );
            
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
       
       $categories = categoryPeer::doSelect( $c );
       
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
   
   private static function getPlaybackContextCategoriesIds($partner_id, $playbackContext, $isAncestor) 
   {
       $categoryFilter = new categoryFilter();
      
       if ($isAncestor)
           $categoryFilter->set("_matchor_likex_full_name", $playbackContext);
       else
           $categoryFilter->set("_in_full_name", $playbackContext);
       
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $categoryFilter->attachToCriteria($c);
       $categoryFilter->setPartnerSearchScope($partner_id);
       $c->applyFilters();
                       
       $categoryIdsFromDB = $c->getFetchedIds();
                       
       if (count($categoryIdsFromDB))
           return $categoryIdsFromDB;
       else
           return array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
     
   }
   
   
   private static function getTotalTableCount($partner_id, $report_type, reportsInputFilter $input_filter, $intervals, $druid_filter, $dimension, $object_ids = null)
   {
       
       $cache_key = self::createCacheKey ($partner_id, $report_type, $input_filter, $object_ids );
     
       $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_REPORTS_COUNT);
       
       $total_count = $cache->get($cache_key);
       if ($total_count)
       {
           KalturaLog::log("count from cache: [$total_count]");
           return $total_count;
       }
       
       $event_type = array_key_exists(self::REPORT_CARDINALITY_METRIC, self::$reportsDef[$report_type]) ? self::$reportsDef[$report_type][self::REPORT_CARDINALITY_METRIC] : self::EVENT_TYPE_PLAYER_IMPRESSION;
       
       $query = self::getDimCardinalityReport($partner_id, $intervals, $dimension, $druid_filter, $event_type);
       
       $total_count_arr = self::runReport($query);
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
       $key = 'reportCount-'.md5("$partner_id|$report_type|$object_ids|".serialize($input_filter));
       return $key;
   }
}
