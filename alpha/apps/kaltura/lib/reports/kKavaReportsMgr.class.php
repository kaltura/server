<?php

class kKavaReportsMgr extends kKavaBase
{
    const METRIC_TOTAL_PLAY_TIME = "playTimeSumMin";
    const METRIC_TOTAL_PLAY_TIME_SEC = "playTimeSum";
    const METRIC_AVG_PLAY_TIME = "playTimeAvg";
    const METRIC_PLAYER_IMPRESSION_RATIO = "playerImpressionRatio";
    const METRIC_AVG_DROP_OFF = "avgDropOffRatio";
    const METRIC_PLAYTHROUGH_RATIO = "playThroughRatio";
    const METRIC_TOTAL_ENTRIES = "totalEntries";
    const METRIC_UNIQUE_USERS = "uniqueUsers";
    const METRIC_UNIQUE_USER_IDS = "uniqueUserIds";
    const METRIC_PLAYTHROUGH = "playThrough";
    const METRIC_TOTAL_COUNT = "total_count";

    const REPORT_DIMENSION = "report_dimension";
    const REPORT_METRICS = "report_metrics";
    const REPORT_DETAIL_DIM_HEADERS = "report_deatil_dimensions_headers";
    const REPORT_GRAPH_METRICS = "report_graph_metrics";
    const REPORT_ENRICH_DEF = "report_enrich_definition";
    const REPORT_GRANULARITY = "report_granularity";
    const REPORT_ENRICH_FIELD = "report_enrich_field";
    const REPORT_ENRICH_FUNC = "report_enrich_func";
    const REPORT_TOTAL_ADDITIONAL_METRICS = "report_total_metrics";
    const REPORT_DRILLDOWN_GRANULARITY = "report_drilldown_granularity";
    const REPORT_DRILLDOWN_DIMENSION = "report_drilldown_dimension";
    const REPORT_DRILLDOWN_METRICS = "report_drilldown_metrics";
    const REPORT_DRILLDOWN_DETAIL_DIM_HEADERS = "report_drilldown_deatil_dimensions_headers";
    const REPORT_CARDINALITY_METRIC = "report_caredinality_metric";
    
    const CLIENT_TAG_PRIORITY = 5;
    const MAX_RESULT_SIZE = 12000;
    
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
    
    private static $time_series_query_template = 
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
        self::EVENT_TYPE_PLAY_END,
        self::METRIC_PLAYTHROUGH,
        self::METRIC_TOTAL_ENTRIES,
        self::METRIC_TOTAL_PLAY_TIME_SEC,
        self::METRIC_UNIQUE_USERS);
    
    private static $simple_event_type_aggrs = array(
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
    
    static $reports_def = array(
        myReportsMgr::REPORT_TYPE_TOP_CONTENT => array(
            self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")   
        ),
        myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF => array(
            self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames"),
            self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
        ),
        myReportsMgr::REPORT_TYPE_CONTENT_INTERACTIONS => array(
            self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")
        ),
        myReportsMgr::REPORT_TYPE_MAP_OVERLAY => array(
            self::REPORT_DIMENSION => self::DIMENSION_LOCATION_COUNTRY,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "country"),
            self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_LOCATION_CITY,
            self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),           
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("object_id","location")
        ),
        myReportsMgr::REPORT_TYPE_TOP_SYNDICATION => array(
            self::REPORT_DIMENSION => self::DIMENSION_DOMAIN,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id","domain_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_URL,
            self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("referrer")
        ),
        myReportsMgr::REPORT_TYPE_USER_ENGAGEMENT => array(
            self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME ,self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),            
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
        ),
        myReportsMgr::REPORT_TYPE_SPECIFIC_USER_ENGAGEMENT => array(
            self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames"),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
        ),
        myReportsMgr::REPORT_TYPE_USER_TOP_CONTENT => array(
            self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS =>  array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
            
        ),
        myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF => array(
            self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS),
            self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
            
        ),
        myReportsMgr::REPORT_TYPE_USER_CONTENT_INTERACTIONS => array(
            self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
            self::REPORT_TOTAL_ADDITIONAL_METRICS => array(self::METRIC_UNIQUE_USERS)
            
        ),
        myReportsMgr::REPORT_TYPE_APPLICATIONS => array(
            self::REPORT_DIMENSION => self::DIMENSION_APPLICATION,
            self::REPORT_METRICS => array(),
            self::REPORT_DETAIL_DIM_HEADERS => array("name"),
            self::REPORT_GRAPH_METRICS => array("application"),
        ),
        myReportsMgr::REPORT_TYPE_PLATFORMS => array(
            self::REPORT_DIMENSION => self::DIMENSION_DEVICE,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_DETAIL_DIM_HEADERS => array("device"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_OS,
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("os")
        ),
        myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM => array(
            self::REPORT_DIMENSION => self::DIMENSION_OS,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("os"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_BROWSER,
            self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array("browser")
        ),
        myReportsMgr::REPORT_TYPE_BROWSERS => array(
            self::REPORT_DIMENSION => self::DIMENSION_BROWSER,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
            self::REPORT_DETAIL_DIM_HEADERS => array("browser"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
        
        ),
        myReportsMgr::REPORT_TYPE_LIVE => array(
            self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
            self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_HOUR,
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "entry_name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "entry_name", self::REPORT_ENRICH_FUNC => "self::getEntriesNames")
        ),
        
        myReportsMgr::REPORT_TYPE_TOP_PLAYBACK_CONTEXT => array(
            self::REPORT_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
            self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
            self::REPORT_DETAIL_DIM_HEADERS => array("object_id", "name"),
            self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self:: METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::REPORT_ENRICH_DEF => array(self::REPORT_ENRICH_FIELD => "name", self::REPORT_ENRICH_FUNC => "self::getCategoriesNames")
        )
    );
     
    static $metrics_to_headers = array(self::EVENT_TYPE_PLAY => "count_plays",
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
        self::DIMENSION_OS => "os",
        self::DIMENSION_BROWSER => "browser",
        self::METRIC_TOTAL_ENTRIES => "unique_videos",
        self::METRIC_UNIQUE_USERS => "unique_known_users",
        self::EVENT_TYPE_REPORT_CLICKED => "count_report",
        self::EVENT_TYPE_DOWNLOAD_CLICKED => "count_download",
        self::EVENT_TYPE_SHARE_CLICKED => "count_viral",
        self::EVENT_TYPE_EDIT_CLICKED => "count_edit",
        self::METRIC_PLAYTHROUGH_RATIO => "play_through_ratio",
        self::DIMENSION_LOCATION_COUNTRY => "country",
    );
    
    static $headers_to_metrics = array();
    
    static $transform_metrics = array(
    	self::METRIC_TOTAL_ENTRIES => 'floor', 
    	self::METRIC_UNIQUE_USERS => 'floor',
    	self::DIMENSION_DEVICE => array('kKavaReportsMgr', 'transformDeviceName'),
    	self::DIMENSION_BROWSER => array('kKavaReportsMgr', 'transformBrowserName'),
    	self::DIMENSION_OS => array('kKavaReportsMgr', 'transformOperatingSystemName'),
    	self::DIMENSION_LOCATION_COUNTRY => array('kKavaCountryCodes', 'toShortName'),
    );
    
    protected static function transformDeviceName($name)
    {
    	$name = strtoupper($name);
    	$name = preg_replace('/[^\w]/', '_', $name);
    	return $name;
    }
    
    protected static function untransformDeviceName($name)
    {
    	$name = str_replace('_', ' ', $name);
    	$name = ucfirst(strtolower($name));
    	return $name;
    }
    
    protected static function transformBrowserName($name)
    {
    	$name = str_replace(array('Internet Explorer', 'Microsoft Edge'), array('IE', 'Edge'), $name);
    	$name = preg_replace('/(\w) (\d)/', '$1$2', $name);
    	$name = strtoupper($name);
    	$name = str_replace(array('(',')'), '', $name);
    	$name = preg_replace('/[^\w]/', '_', $name);
    	return $name;
    }
    
    protected static function transformOperatingSystemName($name)
    {
    	$name = str_replace(array('Windows ', '.x'), array('Windows_', ''), $name);
    	$name = preg_replace('/(\w) (\d)/', '$1$2', $name);
    	$name = strtoupper($name);
    	$name = str_replace(array('(',')'), '', $name);
    	$name = preg_replace('/[^\w]/', '_', $name);
    	return $name;
    }
    
    private static function getEventTypeCountAggr($event_type) {
        $count_aggr = self::$event_type_count_aggr_template;
        $count_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = $event_type;
        $count_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = $event_type;
        
        return $count_aggr;
    }
    
    private static function getFieldRatioAggr($agg_name, $field1, $field2) 
    {
        $ratio_aggr = self::$arithmetic_post_aggr_template;
        $ratio_aggr[self::DRUID_NAME] = $agg_name;
        $ratio_aggr[self::DRUID_FUNCTION] = "/";
        $fields = array(array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => $field1, self::DRUID_FIELD_NAME => $field1),
            array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => $field2, self::DRUID_FIELD_NAME => $field2));
        $ratio_aggr[self::DRUID_FIELDS] = $fields;
        
        return $ratio_aggr;
        
    }
    
    private static function init() {
        
        if (self::$metrics_def) 
        {   
            return;
        }
        
        foreach (self::$simple_metrics as $metric) {
            self::$metrics_def[$metric] = array(self::DRUID_AGGR => array($metric));
        }
        
        self::$metrics_def[self::METRIC_TOTAL_PLAY_TIME] = array(self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME_SEC),
            self::DRUID_POST_AGGR => array(self::METRIC_TOTAL_PLAY_TIME));
        self::$metrics_def[self::METRIC_AVG_PLAY_TIME] = array(self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME_SEC, self::EVENT_TYPE_PLAY),
            self::DRUID_POST_AGGR => array(self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME));
        self::$metrics_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
            self::DRUID_POST_AGGR => array(self::METRIC_PLAYER_IMPRESSION_RATIO));
        self::$metrics_def[self::METRIC_AVG_DROP_OFF] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYTHROUGH),
            self::DRUID_POST_AGGR => array(self::METRIC_AVG_DROP_OFF));
        self::$metrics_def[self::METRIC_PLAYTHROUGH_RATIO] = array(self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_100),
            self::DRUID_POST_AGGR => array(self::METRIC_PLAYTHROUGH_RATIO));
        
        foreach (self::$simple_event_type_aggrs as $event_type) {
            self::$aggregations_def[$event_type] = self::getEventTypeCountAggr($event_type);
        }
        
        self::$aggregations_def[self::METRIC_AVG_PLAY_TIME] = self::getFieldRatioAggr(self::METRIC_AVG_PLAY_TIME, self::METRIC_TOTAL_PLAY_TIME, self::EVENT_TYPE_PLAY);
        self::$aggregations_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = self::getFieldRatioAggr(self::METRIC_PLAYER_IMPRESSION_RATIO, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION);
        self::$aggregations_def[self::METRIC_PLAYTHROUGH_RATIO] = self::getFieldRatioAggr(self::METRIC_PLAYTHROUGH_RATIO, self::EVENT_TYPE_PLAYTHROUGH_100, self::EVENT_TYPE_PLAY);
        
        //$play_time_aggr = self::$event_type_count_aggr_template;
        //$play_time_aggr[self::DRUID_FILTER][self::DRUID_VALUE] = self::EVENT_TYPE_PLAY_END;
        //$play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME;
        //$play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_FIELD_NAME] = self::METRIC_TOTAL_PLAY_TIME;
        
        $play_time_aggr = self::$events_types_count_aggr_template;
        $play_time_aggr[self::DRUID_FILTER][self::DRUID_TYPE] = self::DRUID_IN_FILTER;
        $play_time_aggr[self::DRUID_FILTER][self::DRUID_VALUES] = array(self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100);
        $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME_SEC;
        $play_time_aggr[self::DRUID_AGGREGATOR][self::DRUID_FIELD_NAME] = self::METRIC_TOTAL_PLAY_TIME_SEC;
        
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
        
        self::$aggregations_def[self::METRIC_TOTAL_PLAY_TIME_SEC] = $play_time_aggr;
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
        
        $play_time_aggr_min = self::$arithmetic_post_aggr_template;
        $play_time_aggr_min[self::DRUID_NAME] = self::METRIC_TOTAL_PLAY_TIME;
        $play_time_aggr_min[self::DRUID_FUNCTION] = "/";
        $fields = array(array(self::DRUID_TYPE => self::DRUID_FIELD_ACCESS, self::DRUID_NAME => self::METRIC_TOTAL_PLAY_TIME_SEC, self::DRUID_FIELD_NAME => self::METRIC_TOTAL_PLAY_TIME_SEC),
            array(self::DRUID_TYPE => self::DRUID_CONSTANT, self::DRUID_NAME => "seconds", "value" => 60));
        $play_time_aggr_min[self::DRUID_FIELDS] = $fields;
        self::$aggregations_def[self::METRIC_TOTAL_PLAY_TIME] = $play_time_aggr_min;
        
        foreach (self::$metrics_to_headers as $metric => $header)
        {
            self::$headers_to_metrics[$header] = $metric;
        }    
    }
    
    public static function getGraph ($partner_id, $report_type, reportsInputFilter $input_filter, $dimension = null, $object_ids = null)
    {
        self::init();
        $start = microtime(true);
        $intervals = self::getFilterIntervals($report_type, $input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimension($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        if (!$metrics)
        {
            throw new Exception("unsupported query - report has no metrics");
        }
        
        $report_def = self::$reports_def[$report_type];
        if ($object_ids && array_key_exists(self::REPORT_DRILLDOWN_GRANULARITY, $report_def))
            $granularity = $report_def[self::REPORT_DRILLDOWN_GRANULARITY];
        else if (array_key_exists(self::REPORT_GRANULARITY, $report_def))
            $granularity = $report_def[self::REPORT_GRANULARITY];
        else
            $granularity = self::DRUID_GRANULARITY_DAY;
            
        $granularity = self::getGranularityDef($granularity, $input_filter->timeZoneOffset);
        
        switch ($report_type) 
        { 
            case myReportsMgr::REPORT_TYPE_PLATFORMS:
            case myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM:
            case myReportsMgr::REPORT_TYPE_BROWSERS:
                $query = self::getGroupByReport($partner_id, $intervals, $granularity, array($dimension), $metrics, $druid_filter);
                break;
            default:
                $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $druid_filter);
                
        }
        
        $result = self::runQuery($query);
        KalturaLog::log("Druid returned [" . count($result) . "] rows");
        
        $graph_metrics = $report_def[self::REPORT_GRAPH_METRICS];
        foreach ($graph_metrics as $column)
        {
            $graph_metrics_to_headers[$column] = self::$metrics_to_headers[$column];
        }
        switch ($report_type)
        {
            case myReportsMgr::REPORT_TYPE_PLATFORMS:
                if ($object_ids != NULL && count($object_ids) > 0)
                    $res = self::getMultiGraphsByColumnName($result, $graph_metrics_to_headers, self::DIMENSION_OS, self::$transform_metrics[self::DIMENSION_OS]);
                else
                    $res = self::getMultiGraphsByDateId ($result, self::DIMENSION_DEVICE, $graph_metrics_to_headers, self::$transform_metrics[self::DIMENSION_DEVICE], $input_filter->timeZoneOffset);
                break;
            case myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM:
            case myReportsMgr::REPORT_TYPE_BROWSERS:
                $dimension = $report_def[self::REPORT_DIMENSION];
                $res = self::getMultiGraphsByColumnName($result, $graph_metrics_to_headers, $dimension, self::$transform_metrics[$dimension]);
                break;
            case myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF:
            case myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF:
                $res = self::getGraphsByColumnName($result, $graph_metrics_to_headers, myReportsMgr::$type_map[$report_type]);
                break;
            default:
                $res = self::getGraphsByDateId ($result ,$graph_metrics_to_headers, $input_filter->timeZoneOffset, $report_type == myReportsMgr::REPORT_TYPE_LIVE);    
        }
               
        $end = microtime(true);
        KalturaLog::log("getGraph took [" . ($end - $start) . "]");
        
        return $res;
    }
    
    public static function getTotal($partner_id ,$report_type ,reportsInputFilter $input_filter ,$object_ids = null)
    {
        self::init();
        $start = microtime (true);
        $intervals = self::getFilterIntervals($report_type, $input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimension($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        if (!$metrics)
        {
            throw new Exception("unsupported query - report has no metrics");
        }
        
        $granularity = self::DRUID_GRANULARITY_ALL;
        $report_def = self::$reports_def[$report_type];
        if (array_key_exists(self::REPORT_TOTAL_ADDITIONAL_METRICS, $report_def))
            $metrics = array_merge($report_def[self::REPORT_TOTAL_ADDITIONAL_METRICS], $metrics);
        $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $druid_filter);
        $result = self::runQuery($query);
        if (count($result) > 0)
        {
            $row = $result[0];
            $row_data = $row[self::DRUID_RESULT];
           
            foreach ($metrics as $column)
            {
                $headers[] = self::$metrics_to_headers[$column];
                $data[] = $row_data[$column];
            }
            
            foreach (self::$transform_metrics as $metric => $func) {
                $field_index = array_search(self::$metrics_to_headers[$metric], $headers);
                if (false !== $field_index) {
                    $data[$field_index] = call_user_func($func, $data[$field_index]);
                }
            }                
        }
        else
        {
            foreach ($metrics as $column)
            {
                $headers[] = self::$metrics_to_headers[$column];
                $data[] = '';
            }
        }
        
        $res = array ($headers, $data);   
        $end = microtime(true);
        KalturaLog::log("getTotal took [" . ($end - $start) . "]");
        
        return $res;
    }
    
    
    public static function getTable($partner_id ,$report_type ,reportsInputFilter $input_filter,
        $page_size, $page_index, $order_by, $object_ids = null)
    {
        self::init();
        $start = microtime (true);
        $total_count = null;
        
        $report_def = self::$reports_def[$report_type];
        $intervals = self::getFilterIntervals($report_type, $input_filter);
        $druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, $object_ids);
        $dimension = self::getDimension($report_type, $object_ids);
        $metrics = self::getMetrics($report_type);
        
        if (!$metrics)
        {
        	$query = self::getSearchReport($partner_id, $intervals, array($dimension), $druid_filter);
        	$result = self::runQuery($query);
        	        	 
        	$data = array();
        	if ($result)
        	{
	        	$rows = $result[0][self::DRUID_RESULT];
	        	KalturaLog::log("Druid returned [" . count($rows) . "] rows");
	        	foreach ($rows as $row)
	        	{
	        		$data[] = array($row[self::DRUID_VALUE]);
	        	}
        	}
        	return array($report_def[self::REPORT_DETAIL_DIM_HEADERS], $data, count($data));
        }

        // pager
        if (!$page_size || $page_size < 0) 
            $page_size = 10;
        
        if (!$page_index || $page_index < 0) 
            $page_index = 1;
        
        if ($page_index * $page_size > self::MAX_RESULT_SIZE) 
        {
            throw new Exception("result limit is " . self::MAX_RESULT_SIZE . " rows");
        }
        
        // order by
        $order_by_dir = "-";
        if (!$order_by) 
        {
            $order_by = reset($metrics);
        }
        else
        {
            if ($order_by[0] === "-" || $order_by[0] === "+") {
                $order_by_dir = $order_by[0];
                $order_by = substr($order_by, 1);
            }
            
            if (isset(self::$headers_to_metrics[$order_by]))
                $order_by = self::$headers_to_metrics[$order_by];
            else 
                throw new Exception("Order by parameter is not a valid column");
                
        }
        
        // Note: using a larger threshold since topN is approximate
        $threshold = min(self::MAX_RESULT_SIZE, $page_size * $page_index * 2);
        
        if (!$object_ids &&
        	!in_array($dimension, array(self::DIMENSION_LOCATION_COUNTRY, self::DIMENSION_DOMAIN, self::DIMENSION_DEVICE)))
        {
        	// get the topN objects first, otherwise the returned metrics can be inaccurate
        	$query = self::getTopReport($partner_id, $intervals, array($order_by), $dimension, $druid_filter, $order_by, $order_by_dir, $threshold, $metrics);
	        $result = self::runQuery($query);
	        if (!$result)
	        {
	        	return array(array(), array(), 0);
	        }
	        
	        $rows = $result[0][self::DRUID_RESULT];
	        $rows_count = count($rows);
	        KalturaLog::log("Druid returned [$rows_count] rows");
	         
	        $rows = array_slice($rows, ($page_index - 1) * $page_size, $page_size);
	        if (!$rows)
	        {
	        	return array(array(), array(), $rows_count);
	        }

	        if ($page_index * $page_size > $rows_count)
	        {
	        	$total_count = $rows_count;
	        }
	        else if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, myReportsMgr::$end_user_filter_get_count_reports) )
	        {
                $total_count = self::getTotalTableCount($partner_id ,$report_type ,$input_filter, $intervals, $druid_filter, $dimension, $object_ids);
	        }
	         
	        $dimension_ids = array();
	        foreach ($rows as $row)
	        {
	        	$dimension_ids[] = $row[$dimension];
	        }
	        
	        $page_index = 1;
	        $page_size = count($dimension_ids);
	        $threshold = $page_size; 
	        
	        if ($dimension == self::DIMENSION_ENTRY_ID)
	        {
	        	// when the dimension is entry id, we can use a more minimal filter that does not
	        	// contain the dimensions dependent on entry id - categories + playback type
	        	// the partner id filter is retained since otherwise sending a bogus event 
	        	// with non-matching entryId + partnerId will open access to the entry
		        $druid_filter = array(
		        	array(self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID,
		        		self::DRUID_VALUES => array($partner_id)
		        	),
		        );
		        self::addEndUserReportsDruidFilters($partner_id, $input_filter, $druid_filter);
	        }
	        else
	        {
	        	// Note: not passing $dimension_ids as $object_ids since in some reports $object_ids
	        	//		filters by entries, and not by $dimension
	        	$druid_filter = self::getDruidFilter($partner_id, $report_type, $input_filter, null);
	        }
	        
	        $druid_filter[] = array(
	        	self::DRUID_DIMENSION => $dimension,
	        	self::DRUID_VALUES => $dimension_ids
	        );
        }
                
        $query = self::getTopReport($partner_id, $intervals, $metrics, $dimension, $druid_filter, $order_by, $order_by_dir, $threshold);
        $result = self::runQuery($query);
        if (!$result)
        {
        	return array(array(), array(), 0);
        }

        $rows = $result[0][self::DRUID_RESULT];
        $rows_count = count($rows);
        KalturaLog::log("Druid returned [$rows_count] rows");
        
        $rows = array_slice($rows, ($page_index - 1) * $page_size, $page_size);    
        if (!$rows)
        {
        	return array(array(), array(), $rows_count);
        }
		
		if (is_null($total_count))
		{
			if ($page_index * $page_size > $rows_count)
			{
				$total_count = $rows_count;
			}
			else if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, myReportsMgr::$end_user_filter_get_count_reports) )
			{
				$total_count = self::getTotalTableCount($partner_id ,$report_type ,$input_filter, $intervals, $druid_filter, $dimension, $object_ids);
				
				if ($total_count <= 0)
				{
					$end = microtime(true);
					KalturaLog::log("getTable took [" . ($end - $start) . "]");
					return array(array(), array(), 0);
				}
			}
			else
			{
				$total_count = 0;
			}
		}
                
        $dimension_ids = array();
        
        $dimension_headers = $report_def[self::REPORT_DETAIL_DIM_HEADERS];
        $dimension = $report_def[self::REPORT_DIMENSION];
        if ($object_ids)
        {
            if (array_key_exists(self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS, $report_def))
                $dimension_headers = $report_def[self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS];
            if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
                $dimension = $report_def[self::REPORT_DRILLDOWN_DIMENSION];
        }
        
        $headers = $dimension_headers;
        
        foreach ($metrics as $column)
        {
            $headers[] = self::$metrics_to_headers[$column];
        }
        
        $data = array();
        
        foreach ($rows as $row)
        {
            $dimension_ids[] = $row[$dimension];
            $row_data = array();
            $row_data = array_fill(0, count($dimension_headers), $row[$dimension]);
            
            foreach ($metrics as $column)
            {
                $row_data[] = $row[$column];
            }
            $data[] = $row_data;
            
        }
        
        foreach (self::$transform_metrics as $metric => $func) {
            $field_index = array_search(self::$metrics_to_headers[$metric], $headers);
            if (false !== $field_index) {
                $rows_count = count($data);
                for ($i = 0; $i < $rows_count; $i++) {
                    $data[$i][$field_index] = call_user_func($func, $data[$i][$field_index]);
                }
            }
        }
        
        if (array_key_exists(self::REPORT_ENRICH_DEF, $report_def)) {
            $enrich_func = $report_def[self::REPORT_ENRICH_DEF][self::REPORT_ENRICH_FUNC];
            $entities = call_user_func($enrich_func, $dimension_ids, $partner_id);
            $enrich_field = array_search($report_def[self::REPORT_ENRICH_DEF][self::REPORT_ENRICH_FIELD], $headers);
            if (!$enrich_field) {
                $enrich_field = 0;
            }
            
            $rows_count = count($data);
            for ($i = 0; $i < $rows_count; $i++) {
                $data[$i][$enrich_field] = $entities[$data[$i][$enrich_field]];
            }
        }
		
        $res = array ($headers, $data, $total_count);
        
        $end = microtime(true);
        KalturaLog::log("getTable took [" . ($end - $start) . "]");
        
        return $res;
        
        
    }
    
   private static function getGranularityDef($granularity, $timezone_offset) 
   {
      // choose a timezone from - http://joda-time.sourceforge.net/timezones.html
      // prefer a timezone relative to GMT, so that it won't be affected by DST
      $timezone_name = null;
      if ($timezone_offset % 60 == 0)
      {
         $offset_hours = intval($timezone_offset / 60);
         if ($offset_hours >= -14 && $offset_hours < 0)
         {
            $timezone_name = 'Etc/GMT' . $offset_hours;
         }
         else if ($offset_hours > 0 && $offset_hours <= 12)
         {
            $timezone_name = 'Etc/GMT+' . $offset_hours;
         }
         else if ($offset_hours == 0)
         {
            $timezone_name = 'Etc/GMT';
         }
      }
   	
      if (!$timezone_name)
      {
         $timezone_name = timezone_name_from_abbr("", $timezone_offset * 60 * -1, 0);
      }
      switch ($granularity)
      {
        case self::DRUID_GRANULARITY_DAY:
            $granularity_def = array(self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
                                    self::DRUID_GRANULARITY_PERIOD => "P1D",
                                    self::DRUID_TIMEZONE => $timezone_name);
            break;
        case self::DRUID_GRANULARITY_HOUR:
            $granularity_def = array(self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
                                    self::DRUID_GRANULARITY_PERIOD => "PT1H",
                                    self::DRUID_TIMEZONE => $timezone_name);
            break;
        default:
            $granularity_def = self::DRUID_GRANULARITY_ALL;
      }
      return  $granularity_def;
   }
    
   private static function getFilterIntervals($report_type, $input_filter) {
       if ($report_type == myReportsMgr::REPORT_TYPE_APPLICATIONS)
       {
       	  // the applications report does not depend on from_day/to_day, use the last 30 days
       	  $input_filter->from_day = date('Ymd', time() - 30 * 86400);
       	  $input_filter->to_day = date('Ymd', time());
       	  $input_filter->timeZoneOffset = 0;
       }   	
   	
       $input_filter->timeZoneOffset = round($input_filter->timeZoneOffset / 30) * 30;
       $fromDate = self::dateIdToInterval($input_filter->from_day, $input_filter->timeZoneOffset);
       $toDate = self::dateIdToInterval($input_filter->to_day, $input_filter->timeZoneOffset, true);
       if (!$fromDate || !$toDate || strcmp($toDate, $fromDate) < 0)
       {
       	  $fromDate = $toDate = '2010-01-01T00:00:00+00:00';
       } 
       $intervals = array($fromDate . "/" . $toDate);
       return $intervals;  
   }
    
   private static function getDimension($report_type, $object_ids) {
       $report_def = self::$reports_def[$report_type];
       if ($object_ids && array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
           return $report_def[self::REPORT_DRILLDOWN_DIMENSION];
       
           return $report_def[self::REPORT_DIMENSION];        
   }
    
   private static function getMetrics($report_type) {
       return self::$reports_def[$report_type][self::REPORT_METRICS];
   }
    
   private static function dateIdToInterval($date_id, $offset, $end_of_the_day = false) 
   {
       if (strlen($date_id) < 8 || !preg_match('/^\d+$/', substr($date_id, 0, 8)))
       {
          return null;
       }

       $year = substr($date_id, 0, 4);
       $month = substr($date_id, 4, 2);
       $day = substr($date_id, 6, 2);
       
       $timezone_offset = sprintf("%s%02d:%02d", $offset <= 0 ? '+' : '-', intval(abs($offset)/60), abs($offset) % 60);
       $time = $end_of_the_day? "T23:59:59" : "T00:00:00";
       
       return "$year-$month-$day$time$timezone_offset";
       
   }
   
   // shift date by tz offset
   private static function timestampToDateId($timestamp, $offset) 
   {
       $date = new DateTime($timestamp);
       $date->modify((12 * 60 - $offset) . " minute");		// adding 12H in order to round to the nearest day
       return $date->format('Ymd');
   }
   
   // hours are returned from druid query with the right offset so no need to change it
   private static function timestampToHourId($timestamp)
   {
       $date = new DateTime($timestamp);
       return $date->format('YmdH');
   }
   
   private static function addEndUserReportsDruidFilters($partner_id, $input_filter, &$druid_filter)
   {
   		if (!($input_filter instanceof endUserReportsInputFilter))
   		{
   			return;
   		}
   	
   		if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
	   	{
	   		 
	   		if ($input_filter->playbackContext && $input_filter->ancestorPlaybackContext)
	   			$category_ids = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
	   		else
	   			$category_ids = self::getPlaybackContextCategoriesIds($partner_id, $input_filter->playbackContext ?
	   					$input_filter->playbackContext : $input_filter->ancestorPlaybackContext, isset($input_filter->ancestorPlaybackContext));
	   		 
	   		$druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
	   				self::DRUID_VALUES => $category_ids);
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
   
   private static function getDruidFilter($partner_id, $report_type, $input_filter, $object_ids) 
   {
       $druid_filter = array();
       $druid_filter[] = array(
           self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE,
           self::DRUID_VALUES => $report_type == myReportsMgr::REPORT_TYPE_LIVE ? 
       			array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR) : 
       			array(self::PLAYBACK_TYPE_VOD));
       
       self::addEndUserReportsDruidFilters($partner_id, $input_filter, $druid_filter);
       
       if ($input_filter->categories)
       {
           $category_ids = self::getCategoriesIds($input_filter->categories, $partner_id);
           $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES,
               self::DRUID_VALUES => $category_ids
           );
       }
       
       $entry_ids_from_db = array();
       if ($input_filter->keywords)
       {
           $entry_filter = new entryFilter();
           $entry_filter->setPartnerSearchScope($partner_id);
           
           if($input_filter->search_in_tags)
               $entry_filter->set("_free_text", $input_filter->keywords);
           else
               $entry_filter->set("_like_admin_tags", $input_filter->keywords);
           
           $c = KalturaCriteria::create(entryPeer::OM_CLASS);
           $entry_filter->attachToCriteria($c);
           $c->applyFilters();
           
           $entry_ids_from_db = $c->getFetchedIds();
           
           if ($c->getRecordsCount() > count($entry_ids_from_db))
               throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL );
               
           if (!count($entry_ids_from_db))
               $entry_ids_from_db[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;
               
       }
       
       if($object_ids)
       {
           $object_ids_arr = explode(",", $object_ids);
           
           switch ($report_type)
           {
               case myReportsMgr::REPORT_TYPE_PLATFORMS:
                   $object_ids_arr = array_map(array('kKavaReportsMgr', 'untransformDeviceName'), $object_ids_arr);
                   // fallthrough
           	
               case myReportsMgr::REPORT_TYPE_TOP_SYNDICATION:
               case myReportsMgr::REPORT_TYPE_MAP_OVERLAY:
                   $druid_filter[] = array(self::DRUID_DIMENSION => self::$reports_def[$report_type][self::REPORT_DIMENSION],
                   self::DRUID_VALUES => $object_ids_arr
                   );
                   break;
               default:
                   $entry_ids_from_db = array_merge($object_ids_arr, $entry_ids_from_db);
           }
       }
       
       if (count($entry_ids_from_db))
       {
           $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_ENTRY_ID,
               self::DRUID_VALUES => $entry_ids_from_db
           );
       }
       
       $druid_filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID,
               self::DRUID_VALUES => array($partner_id)
           );         
       
       return $druid_filter;
   } 
   
   private static function getBaseReportDef($partner_id, $intervals, $metrics, $filter, $granularity, $filterMetrics = null) {
       $client_tag = kCurrentContext::$client_lang;
     
       if (kConf::hasParam('kava_top_priority_client_tags')) {
           $priority_tags = kConf::get('kava_top_priority_client_tags');
           foreach ($priority_tags as $tag) 
           {
               if (strpos($client_tag, $tag) === 0) 
               {
                   $report_def[self::DRUID_CONTEXT] = array(self::DRUID_PRIORITY => self::CLIENT_TAG_PRIORITY);
                   break;
               }
           }
       }
       $report_def[self::DRUID_DATASOURCE] = self::HISTORICAL_DATASOURCE;
       $report_def[self::DRUID_INTERVALS] = $intervals;
       $report_def[self::DRUID_GRANULARITY] = $granularity;
       
       // aggregations
       $report_def[self::DRUID_AGGR] = array();
       $report_def[self::DRUID_POST_AGGR] = array();
       foreach ($metrics as $metric)
       {
           if (!array_key_exists($metric, self::$metrics_def))
               continue;
           $metric_aggr = self::$metrics_def[$metric];
           foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) {
                if (in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR]))
                    continue;
                   
                $report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
           }
           if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr))
           {
                foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr) {
                    if (in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_POST_AGGR]))
                        continue;
                    $report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
                }
           } 
       }
       
       // event types
       $event_types = array();
       if (!$filterMetrics)
       {
       		$filterMetrics = $metrics;
       }
       foreach ($filterMetrics as $metric)
       {
       		if (!array_key_exists($metric, self::$metrics_def))
       			continue;
       	  
         	$metric_aggr = self::$metrics_def[$metric];
         	foreach ($metric_aggr[self::DRUID_AGGR] as $aggr) 
         	{
           		if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE]))
           			$event_types[] = self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE];
           		else if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]))
           			$event_types = array_merge($event_types, self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]);
         	}
       }
       	 
       if (count($event_types))
            $filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
                self::DRUID_VALUES => array_values(array_unique($event_types)));
       
       // filter
       $filter_def = self::buildFilter($filter);
       $report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => "and",
           self::DRUID_FIELDS => $filter_def);
       
       return $report_def;
   }
  
   private static function getTopReport($partner_id, $intervals, $metrics, $dimensions, $filter, $order_by, $order_dir, $threshold, $filterMetrics = null) 
   {
       $report_def = self::getBaseReportDef($partner_id, $intervals, $metrics, $filter, self::DRUID_GRANULARITY_ALL, $filterMetrics);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TOPN;
       $report_def[self::DRUID_DIMENSION] = $dimensions;
       $order_type = $order_dir === "+" ? self::DRUID_INVERTED : self::DRUID_NUMERIC;
       $report_def[self::DRUID_METRIC] = array(self::DRUID_TYPE => $order_type,
                                               self::DRUID_METRIC => $order_by);
       $report_def[self::DRUID_THRESHOLD] = $threshold;
             
       return $report_def;
   }
   
   private static function getSearchReport($partner_id, $intervals, $dimensions, $filter) 
   {
       $report_def = self::getBaseReportDef($partner_id, $intervals, array(), $filter, self::DRUID_GRANULARITY_ALL);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_SEARCH;
       $report_def[self::DRUID_SEARCH_DIMENSIONS] = $dimensions;
       $report_def[self::DRUID_QUERY] = array(
	       self::DRUID_TYPE => self::DRUID_CONTAINS,
	       self::DRUID_CASE_SENSITIVE => true,
	       self::DRUID_VALUE => ""
       );
        
       return $report_def;
   }
   
   private static function buildFilter($filters) {
       $filters_def = array();
       foreach ($filters as $filter) {
           if (count($filter[self::DRUID_VALUES]) == 1)
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
       if (!isset($report_def[self::DRUID_CONTEXT]))
           $report_def[self::DRUID_CONTEXT] = array();
       $report_def[self::DRUID_CONTEXT][self::DRUID_SKIP_EMPTY_BUCKETS] = "true";
       return $report_def;
   }
   
   private static function getDimCardinalityReport($partner_id, $intervals, $dimension, $filter, $event_type)
   { 
       if (!$filter)
           $filter = array();
       $filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
                         self::DRUID_VALUES => array($event_type));
       
       $report_def = self::getBaseReportDef($partner_id, $intervals, array(), $filter, self::DRUID_GRANULARITY_ALL);
       $report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;
       
       $report_def[self::DRUID_AGGR][] = array(self::DRUID_TYPE => self::DRUID_CARDINALITY,
                                               self::DRUID_NAME => self::METRIC_TOTAL_COUNT,
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
   
   public static function getGraphsByDateId ($result, $graph_metrics_to_headers, $tz_offset, $is_hourly = false)
   {
       $graphs = array();
       
       foreach ($graph_metrics_to_headers as $column => $header)
       {
           $graphs[$header] = array();
       }
       
       foreach ($result as $row)
       {
           $row_data = $row[self::DRUID_RESULT];
           
           if ($is_hourly)
               $date = self::timestampToHourId($row[self::DRUID_TIMESTAMP]);
           else
               $date = self::timestampToDateId($row[self::DRUID_TIMESTAMP], $tz_offset);
         
           foreach ($graph_metrics_to_headers as $column => $header)
           {
               $graphs[$header][$date] = $row_data[$column];
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByDateId ($result, $multiline_column, $graph_metrics_to_headers, $transform, $tz_offset)
   {
       $graphs = array();
       
       unset($graph_metrics_to_headers[$multiline_column]);
       foreach ($graph_metrics_to_headers as $column => $header)
       {
            $graphs[$header] = array();
       }
       
       foreach ($result as $row)
       {
           $row_data = $row[self::DRUID_EVENT];
           
           $date = self::timestampToDateId($row[self::DRUID_TIMESTAMP], $tz_offset);
           $multiline_val = call_user_func($transform, $row_data[$multiline_column]);
           foreach ($graph_metrics_to_headers as $column => $header)
           {
               if (isset($graphs[$header][$date]))
                   $graphs[$header][$date] .=   ",";
               else 
                   $graphs[$header][$date] = "";
               
               $graphs[$header][$date] .= $multiline_val . ":" .  $row_data[$column];
               
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByColumnName ($result , $graph_metrics_to_headers, $dimension, $transform)
   {
       $graphs = array();
       
       foreach ($graph_metrics_to_headers as $column => $header)
       {
           $graphs[$header] = array();
       }
       
       foreach ($result as $row)
       {
           $row_data = $row[self::DRUID_EVENT];
           
           $dim_value = call_user_func($transform, $row_data[$dimension]);
           
           foreach ($graph_metrics_to_headers as $column => $header)
           {
               $graphs[$header][$dim_value] = $row_data[$column];
           }
       }
       return $graphs;
   }
   
   public static function getGraphsByColumnName($result, $graph_metrics_to_headers, $type_str)
   {
       $graph = array();
       $row = $result[0];
       if (isset($row))
       {
           $row_data = $row[self::DRUID_RESULT];
           foreach ($graph_metrics_to_headers as $column => $header)
           {
               $graph[$header] = $row_data[$column];
           }
       }
       else
       {
   			$graph = array_combine(
   				array_values($graph_metrics_to_headers),
       			array_fill(0, count($graph_metrics_to_headers), 0));
       }
       
       return array($type_str => $graph);
   }
   
   
   private static function getEntriesNames($ids, $partner_id)
   {
       $c = KalturaCriteria::create(entryPeer::OM_CLASS);
       $c->add(entryPeer::PARTNER_ID, $partner_id);
       $c->add(entryPeer::ID, $ids, Criteria::IN);
       
       entryPeer::setUseCriteriaFilter (false);
       $entries = entryPeer::doSelect($c);
       entryPeer::setUseCriteriaFilter (true);
            
       if (!$entries) return null;
       $entries_names = array();
       foreach ($entries  as $entry)
       {
           $entries_names[$entry->getId()] = $entry->getName();
       }
       return $entries_names;

       
   }

   private static function getCategoriesNames($ids, $partner_id)
   {
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $c->add(categoryPeer::PARTNER_ID, $partner_id);
       $c->add(categoryPeer::ID, $ids, Criteria::IN);
       
       categoryPeer::setUseCriteriaFilter (false);
       $categories = categoryPeer::doSelect($c);
       categoryPeer::setUseCriteriaFilter (true);
       
       
       if (!$categories) return null;
       $categories_names = array();
       foreach ($categories as $category)
       {
           $categories_names[$category->getId()] = $category->getName();
       }
       return $categories_names;
       
       
   }
   
   private static function getCategoriesIds($categories, $partner_id)
   {
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $c->add(categoryPeer::PARTNER_ID, $partner_id);
       $c->add(categoryPeer::FULL_NAME, $categories, Criteria::IN);
       $c->addSelectColumn(categoryPeer::ID);
                       
       $stmt = categoryPeer::doSelectStmt($c);
       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
       if (count($rows))
       {
           $category_ids = array();
           foreach ($rows as $row)
           {
               $category_ids[] = $row['ID'];
           }
       }
       else
       {
            $category_ids = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
       }
       return $category_ids;
       
   }
   
   private static function getPlaybackContextCategoriesIds($partner_id, $playback_context, $is_ancestor) 
   {
       $category_filter = new categoryFilter();
      
       if ($is_ancestor)
           $category_filter->set("_matchor_likex_full_name", $playback_context);
       else
           $category_filter->set("_in_full_name", $playback_context);
       
       $c = KalturaCriteria::create(categoryPeer::OM_CLASS);
       $category_filter->attachToCriteria($c);
       $category_filter->setPartnerSearchScope($partner_id);
       $c->applyFilters();
                       
       $category_ids_from_db = $c->getFetchedIds();
                       
       if (count($category_ids_from_db))
           return $category_ids_from_db;
       else
           return array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
     
   }
   
   
   private static function getTotalTableCount($partner_id, $report_type, reportsInputFilter $input_filter, $intervals, $druid_filter, $dimension, $object_ids = null)
   {
       
       $cache_key = 'reportCount-'.md5("$partner_id|$report_type|$object_ids|".serialize($input_filter));
     
       $cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_REPORTS_COUNT);
       if ($cache) {
           $total_count = $cache->get($cache_key);
           if ($total_count)
           {
               KalturaLog::log("count from cache: [$total_count]");
               return $total_count;
           }
       }
       
       $report_def = self::$reports_def[$report_type];
       $event_type = array_key_exists(self::REPORT_CARDINALITY_METRIC, $report_def) ? 
       $report_def[self::REPORT_CARDINALITY_METRIC] : self::EVENT_TYPE_PLAYER_IMPRESSION;
       
       $query = self::getDimCardinalityReport($partner_id, $intervals, $dimension, $druid_filter, $event_type);
       
       $total_count_arr = self::runQuery($query);
       if (isset($total_count_arr[0][self::DRUID_RESULT][self::METRIC_TOTAL_COUNT]))
       {
           $total_count = floor($total_count_arr[0][self::DRUID_RESULT][self::METRIC_TOTAL_COUNT]);
       }
       else
       {
           $total_count = 0;
       }
       KalturaLog::log("count: [$total_count]");
       
       if ($cache)
            $cache->set($cache_key, $total_count, myReportsMgr::REPORTS_COUNT_CACHE); // store in the cache for next time
       
       return $total_count;
   }
   
}
