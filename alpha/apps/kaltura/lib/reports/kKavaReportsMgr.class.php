<?php

class kKavaReportsMgr
{
    static $aggregations_def = array();
    static $metrics_def = array();
    
    const HISTORICAL_DATASOUTCE = "player-events-historical";
    private static $event_type_count_aggr_template = array("type" => "filtered",
        "filter" => array("type" => "selector",
            "dimension" => "eventType",
            "value" => "value"),
        "aggregator" => array("type" => "longSum",
            "name" => "name",
            "fieldName" => "count"));
    
    private static $events_types_count_aggr_template = array("type" => "filtered",
        "filter" => array("type" => "selector",
            "dimension" => "eventType",
            
            "values" => "value"),
        "aggregator" => array("type" => "longSum",
            "name" => "name",
            "fieldName" => "count"));
    
    
    private static $arithmetic_post_aggr_template = array("type" => "arithmetic",
        "name" => "name",
        "fn" => "function",
    );
    
    private static $top_n_query_template = array("queryType" => "topN",
        "dataSource" => self::HISTORICAL_DATASOUTCE,
        "intervals" => "time_intervals",
        "granularity" => "all",
        "dimension" => "dimension",
        "metric" => "metric",
        "threshold" => "threshould");
    
    private static $time_series_query_teample = array("queryType" => "timeseries",
        "dataSource" => self::HISTORICAL_DATASOUTCE,
        "intervals" => "time_intervals",
        "granularity" => "granularity");
    
    private static $group_by_query_template = array("queryType" => "groupBy",
        "dataSource" => self::HISTORICAL_DATASOUTCE,
        "intervals" => "time_intervals",
        "granularity" => "all",
        "dimensions" => "dimension");
    
    static $reportsGranularity = array("content_dropoff" => "all",
        "live" => "hour");
    
    static $reportsDimensions = array("top_content" => "entryId",
        "content_dropoff" => "entryId",
        "content_interactions" => "entryId",
        "map_overlay" => "location.country",
        "map_overlay_drilldown" => "location.city",
        "top_syndication" => "urlParts.domain" ,
        "top_syndication_drilldown" => "urlParts.canonicalUrl" ,
        "user_engagement" => "userId",
        "specific_user_engagement" => "entryId",
        "user_top_content" => "userId",
        "user_content_dropoff" => "userId",
        "user_content_interactions" => "userId",
        "applications" => "application",
        "platforms" => "userAgent.device",
        "platfrom_drilldown"=> "userAgent.operatingSystem",
        "os" => "userAgent.operatingSystem",
        "os_drilldown" => "userAgent.browser",
        "browsers" => "userAgent.browser",
        "live" => "entryId",
        "top_playback_context" => "playbackType");
    
    static $reportsMetrics = array("top_content" => array("play", "playTimeSum","playTimeAvg","playerImpression","playerImpressionRatio","avgDropOffRatio"),
        "content_dropoff" => array("play","playThrough25","playThrough50","playThrough75","playThrough100","playThroughRatio"),
        "content_interactions" => array("play","editClicked","shareClicked","downloadClicked","reportClicked"),
        "map_overlay" => array("play","playThrough25","playThrough50","playThrough75","playThrough100","playThroughRatio"),
        "map_overlay_drilldown" => array("play","playThrough25","playThrough50","playThrough75","playThrough100","playThroughRatio"),
        "top_syndication" => array("play", "playTimeSum","playTimeAvg","playerImpression","playerImpressionRatio"),
        "top_syndication_drilldown" => array("play", "playTimeSum","playTimeAvg","playerImpression","playerImpressionRatio"),
        "user_engagement" => array("totalEntries", "play", "playTimeSum","playTimeAvg","avgDropOffRatio", "playerImpression","playerImpressionRatio"),
        "specific_user_engagement" => array("totalEntries", "play", "playTimeSum","playTimeAvg","avgDropOffRatio", "playerImpression","playerImpressionRatio"),
        "user_top_content" => "",
        "user_content_dropoff" => array("play","playThrough25","playThrough50","playThrough75","playThrough100","playThroughRatio"),
        "user_content_interactions" => array("play","editClicked","shareClicked","downloadClicked","reportClicked"),
        "applications" => array("playerImpression"),
        "platforms" => array("play", "playTimeSum","playTimeAvg", "playerImpression","playerImpressionRatio"),
        "os" => array("play", "playTimeSum","playTimeAvg", "playerImpression","playerImpressionRatio"),
        "browsers" => array("play", "playTimeSum","playTimeAvg", "playerImpression","playerImpressionRatio"),
        "live" => array("play"),
        "top_playback_context" => array("play", "playTimeSum","playTimeAvg","avgDropOffRatio", "playerImpression","playerImpressionRatio"));
    
    static $detailDimensionHeaders = array("top_content" => array("object_id", "name"),
        "content_dropoff" => array("object_id", "name"),
        "content_interactions" => array("object_id", "name"),
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
        "live" => array("object_id", "name"),
        "top_playback_context" => array("object_id", "name"));
    
    static $metricsToHeaders = array("play" => "count_plays",
        "playTimeSum" => "sum_time_viewed",
        "playTimeAvg" => "avg_time_viewed",
        "playerImpression" => "count_loads",
        "playerImpressionRatio" => "load_play_ratio",
        "avgDropOffRatio" => "avg_view_drop_off",
        "playThrough25" => "count_plays_25",
        "playThrough50" => "count_plays_50",
        "playThrough75" => "count_plays_75",
        "playThrough100" => "count_plays_100",
        "userAgent.device" => "device",
        "totalEntries" => "unique_videos",
        "uniqueUsers" => "unique_known_users",
        "reportClicked" => "count_report",
        "downloadClicked" => "count_download",
        "shareClicked" => "count_viral",
        "editClicked" => "count_edit",
        "playThroughRatio" => "play_through_ratio"
    );
    
    static $reportsGraphMetrics = array("top_content" => array("play", "playTimeSum","playTimeAvg","playerImpression"),
        "content_dropoff" => array("play","playThrough25","playThrough50","playThrough75","playThrough100"),
        "content_interactions" => array("play","editClicked","shareClicked","downloadClicked","reportClicked"),        
        "top_syndication" => array("play", "playTimeSum","playTimeAvg","playerImpression"),
        "user_engagement" => array("play", "playTimeSum","playTimeAvg","avgDropOffRatio"),
        "specific_user_engagement" => array("play","playTimeSum","playTimeAvg","playerImpression"),
        "user_top_content" => array("userId"),
        "user_content_dropoff" => array("play","playThrough25","playThrough50","playThrough75","playThrough100","playThroughRatio"),
        "user_content_interactions" => array("play","editClicked","shareClicked","downloadClicked","reportClicked"),
        "applications" => array("application"),
        "platforms" => array("play", "playTimeSum","playTimeAvg", "playerImpression"),
        "os" => array("play", "playTimeSum","playTimeAvg", "playerImpression"),
        "browsers" => array("play", "playTimeSum","playTimeAvg", "playerImpression"),
        "live" => array("play"),
        "top_playback_context" => array("play", "playTimeSum","playTimeAvg","avgDropOffRatio", "playerImpression","playerImpressionRatio"));
    
    static $reportTotalAdditionalMetrics = array("user_engagement" => array("uniqueUsers"));
    
    static $reportsToEnrich = array("top_content" => array("object_id", "name"),
        "content_dropoff" => array("object_id", "name"),
        "content_interactions" => array("object_id", "name"),
        "live" => array("object_id", "name"),
        "top_playback_context" => array("object_id", "name"));
    
    static $reportEnrichFunc = array("top_content" => "self::getEntriesNames",
        "content_dropoff" => "self::getEntriesNames",
        "content_interactions" => "self::getEntriesNames",
        "user_engagement" => array(),
        "specific_user_engagement" => array(),
        "user_top_content" => array(),
        "user_content_dropoff" => array(),
        "user_content_interactions" => array(),
        "applications" => array(),
        "live" => "self::getEntriesNames",
        "top_playback_context" => "self::getCategoriesNames");
    
    static $transform_metrics = array("totalEntries", "uniqueUsers");
    
    private static function init() {
        
        if (!self::$metrics_def) 
        {
            $plays_aggr = self::$event_type_count_aggr_template;
            $plays_aggr["filter"]["value"] = "play";
            $plays_aggr["aggregator"]["name"] = "play";
        
            $play_time_aggr = self::$event_type_count_aggr_template;
            $play_time_aggr["filter"]["value"] = "playEnd";
            $play_time_aggr["aggregator"]["name"] = "playTimeSum";
            $play_time_aggr["aggregator"]["fieldName"] = "playTimeSum";
            
            $views_aggr = self::$event_type_count_aggr_template;
            $views_aggr["filter"]["value"] = "playerImpression";
            $views_aggr["aggregator"]["name"] = "playerImpression";
            
            $play_through25_aggr = self::$event_type_count_aggr_template;
            $play_through25_aggr["filter"]["value"] = "playThrough25";
            $play_through25_aggr["aggregator"]["name"] = "playThrough25";
            
            $play_through50_aggr = self::$event_type_count_aggr_template;
            $play_through50_aggr["filter"]["value"] = "playThrough50";
            $play_through50_aggr["aggregator"]["name"] = "playThrough50";
            
            $play_through75_aggr = self::$event_type_count_aggr_template;
            $play_through75_aggr["filter"]["value"] = "playThrough75";
            $play_through75_aggr["aggregator"]["name"] = "playThrough75";
            
            $play_through100_aggr = self::$event_type_count_aggr_template;
            $play_through100_aggr["filter"]["value"] = "playThrough100";
            $play_through100_aggr["aggregator"]["name"] = "playThrough100";
            
            $edit_clicked = self::$event_type_count_aggr_template;
            $edit_clicked["filter"]["value"] = "editClicked";
            $edit_clicked["aggregator"]["name"] = "editClicked";
            
            $share_clicked = self::$event_type_count_aggr_template;
            $share_clicked["filter"]["value"] = "shareClicked";
            $share_clicked["aggregator"]["name"] = "shareClicked";
            
            $download_clicked = self::$event_type_count_aggr_template;
            $download_clicked["filter"]["value"] = "downloadClicked";
            $download_clicked["aggregator"]["name"] = "downloadClicked";
            
            $report_clicked= self::$event_type_count_aggr_template;
            $report_clicked["filter"]["value"] = "reportClicked";
            $report_clicked["aggregator"]["name"] = "reportClicked";
            
            $play_through_aggr = self::$events_types_count_aggr_template;
            $play_through_aggr["filter"]["type"] = "in";
            $play_through_aggr["filter"]["values"] = array("playThrough25","playThrough50","playThrough75","playThrough100");
            $play_through_aggr["aggregator"]["name"] = "playThrough";
            
            $play_end_aggr = self::$event_type_count_aggr_template;
            $play_end_aggr["filter"]["value"] = "playEnd";
            $play_end_aggr["aggregator"]["name"] = "playEnd";
            
            $total_entries_aggr = array("type" => "cardinality",
                "name"=> "totalEntries",
                "fields" => array("entryId"));
            
            $unique_users_aggr = array("type" => "hyperUnique",
                "name" => "uniqueUsers",
                "fieldName" => "uniqueUserIds");
            
        
            self::$metrics_def["playerImpression"] = array("aggregations" => array("playerImpression"));
            self::$metrics_def["playRequested"] = array("aggregations" => array("playRequested"));
            self::$metrics_def["play"] = array("aggregations" => array("play"));
            self::$metrics_def["resume"] = array("aggregations" => array("resume"));
            self::$metrics_def["playThrough25"] = array("aggregations" => array("playThrough25"));
            self::$metrics_def["playThrough50"] = array("aggregations" => array("playThrough50"));
            self::$metrics_def["playThrough75"] = array("aggregations" => array("playThrough75"));
            self::$metrics_def["playThrough100"] = array("aggregations" => array("playThrough100"));
            self::$metrics_def["playThrough"] = array("aggregations" => array("playThrough"));
            self::$metrics_def["editClicked"] = array("aggregations" => array("editClicked"));
            self::$metrics_def["shareClicked"] = array("aggregations" => array("shareClicked"));
            self::$metrics_def["shared"] = array("aggregations" => array("shared"));
            self::$metrics_def["downloadClicked"] = array("aggregations" => array("downloadClicked"));
            self::$metrics_def["reportClicked"] = array("aggregations" => array("reportClicked"));
            self::$metrics_def["reportSubmitted"] = array("aggregations" => array("reportSubmitted"));
            self::$metrics_def["enterFullscreen"] = array("aggregations" => array("enterFullscreen"));
            self::$metrics_def["exitFullscreen"] = array("aggregations" => array("exitFullscreen"));
            self::$metrics_def["pauseClicked"] = array("aggregations" => array("pauseClicked"));
            self::$metrics_def["replay"] = array("aggregations" => array("replay"));
            self::$metrics_def["seek"] = array("aggregations" => array("seek"));
            self::$metrics_def["relatedClicked"] = array("aggregations" => array("relatedClicked"));
            self::$metrics_def["relatedSelected"] = array("aggregations" => array("relatedSelected"));
            self::$metrics_def["captions"] = array("aggregations" => array("captions"));
            self::$metrics_def["sourceSelected"] = array("aggregations" => array("sourceSelected"));
            self::$metrics_def["info"] = array("aggregations" => array("info"));
            self::$metrics_def["speed"] = array("aggregations" => array("speed"));
            self::$metrics_def["view"] = array("aggregations" => array("view"));
            self::$metrics_def["totalEntries"] = array("aggregations" => array("totalEntries"));
            self::$metrics_def["playTimeSum"] = array("aggregations" => array("playTimeSum"));
            self::$metrics_def["playEnd"] = array("aggregations" => array("playEnd"));
            self::$metrics_def["uniqueUsers"] = array("aggregations" => array("uniqueUsers"));
            self::$metrics_def["playTimeAvg"] = array("aggregations" => array("playTimeSum","playEnd"),
                "post_aggregations" => array("playTimeAvg"));
            self::$metrics_def["playerImpressionRatio"] = array("aggregations" => array("play","playerImpression"),
                "post_aggregations" => array("playerImpressionRatio"));
            self::$metrics_def["avgDropOffRatio"] = array("aggregations" => array("play","playThrough"),
                "post_aggregations" => array("avgDropOffRatio"));
            self::$metrics_def["playThroughRatio"] = array("aggregations" => array("play","playThrough100"),
                "post_aggregations" => array("playThroughRatio"));
            
        
            self::$aggregations_def["play"] = $plays_aggr;
            self::$aggregations_def["playTimeSum"] = $play_time_aggr;
            self::$aggregations_def["playerImpression"] = $views_aggr;
            self::$aggregations_def["playThrough"] = $play_through_aggr;
            self::$aggregations_def["playEnd"] = $play_end_aggr;
            self::$aggregations_def["playThrough25"] = $play_through25_aggr;
            self::$aggregations_def["playThrough50"] = $play_through50_aggr;
            self::$aggregations_def["playThrough75"] = $play_through75_aggr;
            self::$aggregations_def["playThrough100"] = $play_through100_aggr;
            self::$aggregations_def["editClicked"] = $edit_clicked;
            self::$aggregations_def["shareClicked"] = $share_clicked;
            self::$aggregations_def["downloadClicked"] = $download_clicked;
            self::$aggregations_def["reportClicked"] = $report_clicked;
            self::$aggregations_def["totalEntries"] = $total_entries_aggr;
            self::$aggregations_def["uniqueUsers"] = $unique_users_aggr;
            
        
        
            $avg_time_viewed = self::$arithmetic_post_aggr_template;
            $avg_time_viewed["name"] = "playTimeAvg";
            $avg_time_viewed["fn"] = "/";
            $fields= array(array("type" => "fieldAccess", "name" => "playTimeSum", "fieldName" => "playTimeSum"),
                array("type" => "fieldAccess", "name" => "playEnd", "fieldName" => "playEnd"));
            $avg_time_viewed["fields"] = $fields;
            
            $player_impression_ratio= self::$arithmetic_post_aggr_template;
            $player_impression_ratio["name"] = "playerImpressionRatio";
            $player_impression_ratio["fn"] = "/";
            $fields= array(array("type" => "fieldAccess", "name" => "play", "fieldName" => "play"),
                array("type" => "fieldAccess", "name" => "playerImpression", "fieldName" => "playerImpression"));
            $player_impression_ratio["fields"] = $fields;
            
            $play_through_ratio = self::$arithmetic_post_aggr_template;
            $play_through_ratio["name"] = "playThroughRatio";
            $play_through_ratio["fn"] = "/";
            $fields= array(array("type" => "fieldAccess", "name" => "playThrough100", "fieldName" => "playThrough100"),
                array("type" => "fieldAccess", "name" => "play", "fieldName" => "play"));
            $play_through_ratio["fields"] = $fields;
            
            $avg_dropoff_ratio = self::$arithmetic_post_aggr_template;
            $avg_dropoff_ratio["name"] = "avgDropOffRatio";
            $avg_dropoff_ratio["fn"] = "/";
            
            $avg_dropoff_ration_sub_calc = self::$arithmetic_post_aggr_template;
            $avg_dropoff_ration_sub_calc["fn"]= "/";
            $avg_dropoff_ration_sub_calc["name"]= "subDropOff";
            $sub_calc_fields = array(array("type" => "fieldAccess", "name" => "playThrough", "fieldName" => "playThrough"),
                array("type" => "constant", "name" => "quater", "value" => "4"));
            $avg_dropoff_ration_sub_calc["fields"] = $sub_calc_fields;
            $avg_dropoff_ratio["fields"] = array($avg_dropoff_ration_sub_calc,  array("type" => "fieldAccess", "name" => "play", "fieldName" => "play"));
            
            self::$aggregations_def["playTimeAvg"] = $avg_time_viewed;
            self::$aggregations_def["playerImpressionRatio"] = $player_impression_ratio;
            self::$aggregations_def["avgDropOffRatio"] = $avg_dropoff_ratio;
            self::$aggregations_def["playThroughRatio"] = $play_through_ratio;
        }    
    }
    
    public static function getGraph ( $partner_id , $report_type , reportsInputFilter $input_filter , $dimension = null , $object_ids = null )
    {
        self::init();
        $start = microtime(true);
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_GRAPH , $input_filter , null , null , null , $object_ids );
        $report_str = myReportsMgr::$type_map[$report_type];
        if ( $report_type == myReportsMgr::REPORT_TYPE_PLATFORMS)
        {
            if ($object_ids != NULL && count($object_ids) > 0)
                $res = self::getGraphsByDateId ( $result , $report_str);
            else
                $res = self::getMultiGraphsByDateId ( $result , "userAgent.device", $report_str);
        }
        else if ( $report_type == myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF|| $report_type == myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF)
        {
            $res = self::getGraphsByColumnName ( $result , $report_str);
        }
        else
        {
            $res = self::getGraphsByDateId ( $result , $report_str);
        }
        
        $end = microtime(true);
        KalturaLog::log( "getGraph took [" . ( $end - $start ) . "]" );
        
        return $res;
    }
    
    public static function getTotal ( $partner_id , $report_type , reportsInputFilter $input_filter , $object_ids = null  )
    {
        self::init();
        $start = microtime ( true );
        
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_TOTAL , $input_filter , null , null , null , $object_ids );
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
        $page_size , $page_index , $order_by , $object_ids = null , $offset = null)
    {
        self::init();
        $start = microtime ( true );
        $total_count = 0;
        
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
       
        if ( ! $page_size || $page_size < 0 ) $page_size = 10;
        //$page_size = min($page_size , self::REPORTS_TABLE_MAX_QUERY_SIZE);
        
        if ( ! $page_index || $page_index < 0 ) $page_index = 0;
        if ($page_index * $page_size > 12000) 
        {
            //todo: result is too big
        }
        $result  = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_TABLE , $input_filter ,$page_size , $page_index , $order_by , $object_ids, $offset );
        if ( count($result) > 0 )
        {
            $report_str = myReportsMgr::$type_map[$report_type];
            $rows = $result[0]->result;
            $rows_count = count($rows);
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
                $reportMetrics = self::$reportsMetrics[$report_str];
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
                    $enrich_field = array_search("name", $headers);
                    if (!$enrich_field) {
                        $enrich_field = 0;
                    }
                    
                    $rows_count = count($data);
                    for ($i = 0; $i < $rows_count; $i++) {
                        $data[$i][$enrich_field] = $entities[$data[$i][$enrich_field]];
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
    
    public static function getReport($partner_id, $type_str , $flavor_str , $object_ids, $input_filter, $filter, $order_by, $order_dir, $threshold, $tz = 0) {
       
       $intervals = array(self::dateIdToInterval($input_filter->from_day, $tz) . "/" .  self::dateIdToInterval($input_filter->to_day, $tz));
       $dimension= "";
       if ($object_ids) 
           if (array_key_exists($type_str ."_drilldown", self::$reportsDimensions)) 
               $dimension = self::$reportsDimensions[$type_str . "_drilldown"];
       if (!$dimension)
           $dimension = self::$reportsDimensions[$type_str];
       $metrics = self::$reportsMetrics[$type_str];
       $query = "";
       switch ($flavor_str) {
        case "graph":
            $granularity = array_key_exists($type_str, self::$reportsGranularity) ? self::$reportsGranularity[$type_str] : "day";
            if ($type_str === "platforms")
                $query = self::getGroupByReport($partner_id, $intervals, $granularity, array($dimension), $metrics, $filter);
            else
                $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter);
            break;
        case "detail":
            $granularity = "all";
            $query = self::getTopReport($partner_id, $intervals, $metrics, $dimension, $filter, $order_by, $order_dir, $threshold);
            break;
        case "total":
            $granularity = "all";
            if (array_key_exists($type_str, self::$reportTotalAdditionalMetrics))
                $metrics = array_merge(self::$reportTotalAdditionalMetrics[$type_str], $metrics); 
            $query = self::getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter);
            break;
        case "count":
            $query = self::getDimCardinalityReport($partner_id, $intervals, $dimension, $filter);
            break;
           
        
       }
       return $query;
   }
   
   private static function dateIdToInterval($dateId, $tz) 
   {
       $year = substr($dateId, 0, 4);
       $month = substr($dateId, 4, 2);
       $day = substr($dateId, 6, 2);
       return "$year-$month-$day" . "T00:00:00.000";
       
   }
   
   private static function timestampToDateId($timestamp) 
   {
       $year = substr($timestamp, 0, 4);
       $month = substr($timestamp, 5, 2);
       $day = substr($timestamp, 8, 2);
       return "$year$month$day";
   }
   
   private static function executeQueryByType ( $partner_id , $report_type , $report_flavor , reportsInputFilter $input_filter  ,
       $page_size , $page_index , $order_by , $object_ids = null , $offset = null)
   {
       $start = microtime(true);
       try
       {
        
           $entryFilter = new entryFilter();
           $entryFilter->setPartnerSearchScope($partner_id);
           $shouldSelectFromSearchEngine = false;
           
           $druid_filter = null;
           if ($input_filter instanceof endUserReportsInputFilter)
           {
               if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
               {
                   $categoryIds = self::getCategoriesIds($input_filter);
                   $druid_filter[] = array("dimension" => "playbackContext",
                       "values" => explode(',', $categoryIds)
                   );
               }
               
               if ($input_filter->application) {
                   $druid_filter[] = array("dimension" => "application",
                       "values" => explode(',', $input_filter->application)
                   );
               }
               if ($input_filter->userIds != null) {
                   $puserIds = "('" . implode("','", $input_filter->userIds) . "')";
                   // replace puser_id '0' with 'Unknown' as it saved on dwh pusers table
                   $puserIds = str_replace(self::UNKNOWN_PUSER_ID_CLAUSE, self::UNKNOWN_NAME_CLAUSE, $puserIds);
                   
                   $puserIds= substr($puserIds, 1, -1);
                   $druid_filter[] = array("dimension" => "userId",
                       "values" => explode("','", $puserIds)
                   );
               }
           }
           
           if ($input_filter->categories)
           {
               $categoryIds = self::getCategoriesIds($input_filter);
               $druid_filter[] = array("dimension" => "categories",
                   "values" => explode(",", $categoryIds)
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
           
           $obj_ids_clause = null;
           
           if($object_ids)
           {
               $object_ids_arr = explode(",", $object_ids);
               
               if ( $report_type == myReportsMgr::REPORT_TYPE_TOP_SYNDICATION )
               {
                   $druid_filter[] = array("dimension" => "urlParts.domain",
                       "values" => $object_ids_arr
                   );
               }
               else if ( $report_type == myReportsMgr::REPORT_TYPE_MAP_OVERLAY )
               {
                   $druid_filter[] = array("dimension" => "location.country",
                       "values" => $object_ids_arr
                   );
               }
               
               else if ( $report_type == myReportsMgr::REPORT_TYPE_PLATFORMS)
               {
                   $druid_filter[] = array("dimension" => "userAgent.device ",
                       "values" => $object_ids_arr
                   );
               }
               else
               {
                   $entryIds = array_merge($object_ids_arr, $entryIdsFromDB);
                   
                   $druid_filter[] = array("dimension" => "entryId",
                       "values" => $entryIds
                   );
                   
               }
           }
           elseif (count($entryIdsFromDB))
           {
               $druid_filter[] = array("dimension" => "entryId",
                   "values" => $entryIdsFromDB
               );
           }
           
               
           if ($input_filter instanceof endUserReportsInputFilter && ($input_filter->userIds != null) && ($report_type == self::REPORT_TYPE_USER_USAGE || $report_type == self::REPORT_TYPE_SPECIFIC_USER_USAGE) ) {
               $userFilter = new kuserFilter();
               $userFilter->set("_in_puser_id", $input_filter->userIds);
               $c = KalturaCriteria::create(kuserPeer::OM_CLASS);
               $userFilter->attachToCriteria($c);
               $c->applyFilters();
               
               $userIdsFromDB = $c->getFetchedIds();
               
               if (count($userIdsFromDB))
                   $kuserIds = implode(",", $userIdsFromDB);
                   else
                       $kuserIds = kuser::KUSER_ID_THAT_DOES_NOT_EXIST;
                       
                       $obj_ids_clause = "u.kuser_id in ( $kuserIds )";
           }
           
           $type_str = myReportsMgr::$type_map[$report_type];
           
           $order_by_dir = "-";
           if (!$order_by) {
               $order_by = self::$reportsMetrics[$type_str][0];
               $order_by_dir = "-";
           }
           else 
           {
               if (substr($order_by, 0, 1) === "-" || substr($order_by, 0, 1) === "+") {
                   $order_by_dir = substr($order_by, 0, 1);
                   $order_by = substr($order_by, 1);
               }
                   
               $order_by = array_search ($order_by, self::$metricsToHeaders);
           }
           
           $flavor_str = myReportsMgr::$flavor_map[$report_flavor];
           $query = self::getReport($partner_id, $type_str, $flavor_str, $object_ids, $input_filter, $druid_filter, $order_by, $order_by_dir, $page_size * $page_index);
                       
           $query_header = "/* -- " .$type_str . " " . $flavor_str . " -- */\n";
           KalturaLog::log( "\n{$query_header}{$query}" );
                               
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
       $report_def["intervals"] = $intervals;
       $report_def["dimension"] = $dimensions;
       $order_type = "numeric";
       if ($order_dir === "+")
           $order_type = "inverted";
       $report_def["metric"] = array("type" => $order_type,
                                     "metric" => $order_by);
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
           $report_def["filter"] = array("type" => "and",
                                         "fields" => $filter_def);
           
       } else { 
           $report_def["filter"] = array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
       }
       
       $report_def["threshold"] = $page_size;
       $report_def["aggregations"] = array();
       $report_def["postAggregations"] = array();
       foreach ($metrics as $metric ) 
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr["aggregations"] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def["aggregations"])))
                        $report_def["aggregations"][] = self::$aggregations_def[$aggr];
               }
               if ($metric_aggr["post_aggregations"]) {
                   foreach ($metric_aggr["post_aggregations"] as $aggr) {
                       $report_def["postAggregations"][] = self::$aggregations_def[$aggr];
                   }
               }
           }
       }
       return $report_def;
   }
   
   private static function buildFilter($filters) {
       $filters_def = array();
       foreach ($filters as $filter) {
           if (sizeof( $filter["values"]) == 1)
                $filters_def[] = array("type" => "selector",
                                  "dimension" => $filter["dimension"],
                                  "value" => $filter["values"][0]
                    
                );
                
           else 
               $filters_def[] = array("type" => "in",
                   "dimension" => $filter["dimension"],
                   "values" => $filter["values"]
               );
       }
       return $filters_def;
   }
   
   private static function getTimeSeriesReport($partner_id, $intervals, $granularity, $metrics, $filter)
   {
       $report_def = self::$time_series_query_teample;
       $report_def["intervals"] = $intervals;
       $report_def["granularity"] = $granularity;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
           $report_def["filter"] = array("type" => "and",
               "fields" => $filter_def);
           
       } else {
           $report_def["filter"] = array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
       }
       $report_def["aggregations"] = array();
       $report_def["postAggregations"] = array();
       foreach ($metrics as $metric )
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr["aggregations"] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def["aggregations"])))
                       $report_def["aggregations"][] = self::$aggregations_def[$aggr];
               }
               if (array_key_exists("post_aggregations", $metric_aggr))
                    foreach ($metric_aggr["post_aggregations"] as $aggr) {
                        $report_def["postAggregations"][] = self::$aggregations_def[$aggr];
                    }
               
           }
       }
       return $report_def;
   }
   
   private static function getDimCardinalityReport($partner_id, $intervals, $dimension, $filter)
   {
       $report_def = self::$time_series_query_teample;
       $report_def["intervals"] = $intervals;
       $report_def["granularity"] = "all";
       if ($filter) {
           $filter_def = self::buildFilter($filter);
       }
       $filter_def[] = array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
       $filter_def[] = array("type"=>"selector", "dimension"=>"eventType", "value" => "playerImpression");
       $report_def["filter"] = array("type" => "and",
               "fields" => $filter_def);
           
       
       $report_def["aggregations"] = array();
       $report_def["postAggregations"] = array();
       $report_def["aggregations"][] = array("type" => "cardinality",
                                             "name" => "total_count",
                                             "fields" => array($dimension));
      
       return $report_def;
   }
   
   private static function getGroupByReport($partner_id, $intervals, $granularity, $dimensions, $metrics, $filter, $pageSize = 0)
   {
       $report_def = self::$group_by_query_template;
       $report_def["intervals"] = $intervals;
       $report_def["granularity"] = $granularity;
       $report_def["dimensions"] = $dimensions;
       if ($filter) {
           $filter_def = self::buildFilter($filter);
           $filter_def[]= array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
           $report_def["filter"] = array("type" => "and",
               "fields" => $filter_def);
           
       } else {
           $report_def["filter"] = array("type"=>"selector", "dimension"=>"partnerId", "value" => $partner_id);
       }
       $report_def["aggregations"] = array();
       $report_def["postAggregations"] = array();
       foreach ($metrics as $metric )
       {
           if (array_key_exists($metric, self::$metrics_def)) {
               $metric_aggr = self::$metrics_def[$metric];
               foreach ($metric_aggr["aggregations"] as $aggr) {
                   if (!(in_array(self::$aggregations_def[$aggr], $report_def["aggregations"])))
                       $report_def["aggregations"][] = self::$aggregations_def[$aggr];
               }
               if ($metric_aggr["post_aggregations"])
               {
                    foreach ($metric_aggr["post_aggregations"] as $aggr) {
                        $report_def["postAggregations"][] = self::$aggregations_def[$aggr];
                    }
               }
               
           }
       }
       return $report_def;
   }
   
   public static function runReport($content) {
       $post = json_encode($content);
       
       $remote_path = "http://52.42.180.203:8082/druid/v2/";
       
       
       
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
   
   public static function getGraphsByDateId ( $result , $report_type )
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
           
           $date = self::timestampToDateId($row->timestamp);
         
           foreach ( $graphMetrics as $column )
           {
               $graph = $graphs[self::$metricsToHeaders[$column]];
               $graph[$date] = $row_data->$column; // the value for graph 1 will be column #1 in the row
               $graphs[self::$metricsToHeaders[$column]] = $graph;
           }
       }
       return $graphs;
   }
   
   public static function getMultiGraphsByDateId ( $result , $multiline_column, $report_type )
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
           
           $date = self::timestampToDateId($row->timestamp);
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
   
   
   private static $count_cache ;
   private static function getTotalTableCount( $partner_id , $report_type , reportsInputFilter $input_filter  ,
       $page_size , $page_index , $order_by , $object_ids = null )
   {
       
       $cache_key = self::createCacheKey ( $partner_id , $report_type , $input_filter , $object_ids );
       if ( ! self::$count_cache )
       {
           self::$count_cache = new myCache( "reportscount" , myReportsMgr::REPORTS_COUNT_CACHE ); // store the cache for
       }
       
       $total_count = self::$count_cache->get( $cache_key );
       if ( $total_count )
       {
           KalturaLog::log( "count from cache: [$total_count]" );
           return $total_count;
       }
       
       $total_count_arr = self::executeQueryByType( $partner_id , $report_type , myReportsMgr::REPORT_FLAVOR_COUNT , $input_filter ,null , null , null , $object_ids );
       if ( $total_count_arr && isset ($total_count_arr[0]->result->total_count ) )
       {
           $total_count = floor($total_count_arr[0]->result->total_count);
       }
       else
       {
           $total_count = 0;
       }
       KalturaLog::log( "count: [$total_count]" );
       
       self::$count_cache->put( $cache_key , $total_count ); // store in the cache for next time
       return $total_count;
   }
   
   private static function createCacheKey ( $partner_id , $report_type , reportsInputFilter  $input_filter , $object_ids )
   {
       if ( strlen( $partner_id ) > 40 )
           $partner_id_str = md5($partner_id);
           else
               $partner_id_str = $partner_id;
               
               $key = 	$partner_id_str . "|" . $report_type . "|" .
                   $input_filter->from_date . $input_filter->to_date . $input_filter->keywords . $input_filter->search_in_admin_tags . $input_filter->search_in_tags . $input_filter->interval .
                   $object_ids . $input_filter->categories;
                   if ($input_filter instanceof endUserReportsInputFilter)
                       $key = $key .  $input_filter->application . $input_filter->userIds . $input_filter->playbackContext . $input_filter->ancestorPlaybackContext;
                       return $key;
   }
}


