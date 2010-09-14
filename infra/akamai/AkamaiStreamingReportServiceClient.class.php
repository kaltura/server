<?php

	
class AkamaiStreamingReportServiceClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/nmrws/services/StreamingReportService?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function getCPCodes()
	{
		$params = array();
		

		$result = $this->call("getCPCodes", $params);
		$this->logError();
		return $result;
	}
	
	public function getCurrentGeoMap($cpcodes, $media, $graphType, $mapScale)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["media"] = $this->parseParam($media, 'akaaimsdt:MediaTypeEnum');
		$params["graphType"] = $this->parseParam($graphType, 'akaaimsdt:GraphTypeEnum');
		$params["mapScale"] = $this->parseParam($mapScale, 'xsd:double');

		$result = $this->call("getCurrentGeoMap", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamRealtimeDataByPort($port, $type, $columns)
	{
		$params = array();
		
		$params["port"] = $this->parseParam($port, 'xsd:int');
		$params["type"] = $this->parseParam($type, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamRealtimeDataByPort", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamRealtimeDataByName($name, $type, $columns)
	{
		$params = array();
		
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["type"] = $this->parseParam($type, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamRealtimeDataByName", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamRealtimeData($type, $columns)
	{
		$params = array();
		
		$params["type"] = $this->parseParam($type, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamRealtimeData", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamCPCodes()
	{
		$params = array();
		

		$result = $this->call("getLiveStreamCPCodes", $params);
		$this->logError();
		return $result;
	}
	
	public function getVodStreamCPCodes()
	{
		$params = array();
		

		$result = $this->call("getVodStreamCPCodes", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTrafficForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamTrafficForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTrafficForCPCodeV2($cpcodes, $start, $end, $timeZone, $columns, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:StreamingReportFilter');

		$result = $this->call("getLiveStreamTrafficForCPCodeV2", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTrafficForReportGroup($repgrp, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamTrafficForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTrafficForReportGroupV2($repgrp, $start, $end, $timeZone, $columns, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:StreamingReportFilter');

		$result = $this->call("getLiveStreamTrafficForReportGroupV2", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamReponseCodesSummaryForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamReponseCodesSummaryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamCPCodeSummaryForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamCPCodeSummaryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamReponseCodesSummaryForReportGroup($repgrp, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamReponseCodesSummaryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUniqueVisitorForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUniqueVisitorForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUniqueVisitorForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUniqueVisitorForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTopVisitorForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamTopVisitorForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamTopVisitorForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamTopVisitorForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByOSForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByOSForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByOSForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByOSForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorBySoftwareForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorBySoftwareForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorBySoftwareForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorBySoftwareForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByCountryForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByCountryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByCountryForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByCountryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocationByCountryForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocationByCountryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocationByCountryForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocationByCountryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByUSStateForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByUSStateForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByUSStateForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByUSStateForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocationByUSStateForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocationByUSStateForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocationByUSStateForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocationByUSStateForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByCAProvinceForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByCAProvinceForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByCAProvinceForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByCAProvinceForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocationByCAProvinceForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocationByCAProvinceForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamUserLocByCAProvinceForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamUserLocByCAProvinceForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByLanguageForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByLanguageForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamVisitorByLanguageForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getLiveStreamVisitorByLanguageForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamURLForCPCode($cpcodes, $startDate, $endDate, $columns, $sortColumn, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getLiveStreamURLForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamURLForReportGroup($repgrp, $startDate, $endDate, $columns, $sortColumn, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getLiveStreamURLForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamURLDailyAggregationForCPCode($cpcodes, $startDate, $endDate, $aggregateColumn, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["aggregateColumn"] = $this->parseParam($aggregateColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getLiveStreamURLDailyAggregationForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveStreamURLDailyAggregationForReportGroup($repgrp, $startDate, $endDate, $aggregateColumn, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["aggregateColumn"] = $this->parseParam($aggregateColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getLiveStreamURLDailyAggregationForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTrafficForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamTrafficForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTrafficForReportGroup($repgrp, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamTrafficForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTrafficForCPCodeV2($cpcodes, $start, $end, $timeZone, $columns, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:StreamingReportFilter');

		$result = $this->call("getVODStreamTrafficForCPCodeV2", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTrafficForReportGroupV2($repgrp, $start, $end, $timeZone, $columns, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:StreamingReportFilter');

		$result = $this->call("getVODStreamTrafficForReportGroupV2", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamReponseCodesSummaryForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamReponseCodesSummaryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamCPCodeSummaryForCPCode($cpcodes, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamCPCodeSummaryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamReponseCodesSummaryForReportGroup($repgrp, $start, $end, $timeZone, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["start"] = $this->parseParam($start, 'xsd:dateTime');
		$params["end"] = $this->parseParam($end, 'xsd:dateTime');
		$params["timeZone"] = $this->parseParam($timeZone, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamReponseCodesSummaryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUniqueVisitorForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUniqueVisitorForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUniqueVisitorForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUniqueVisitorForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTopVisitorForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamTopVisitorForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamTopVisitorForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamTopVisitorForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByOSForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByOSForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByOSForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByOSForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorBySoftwareForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorBySoftwareForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorBySoftwareForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'xsd:string');

		$result = $this->call("getVODStreamVisitorBySoftwareForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByCountryForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByCountryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByCountryForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByCountryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocationByCountryForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocationByCountryForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocationByCountryForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocationByCountryForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByUSStateForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByUSStateForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByUSStateForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByUSStateForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocationByUSStateForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocationByUSStateForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocationByUSStateForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocationByUSStateForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByCAProvinceForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByCAProvinceForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByCAProvinceForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByCAProvinceForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocationByCAProvinceForCPCode($cpcodes, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocationByCAProvinceForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamUserLocByCAProvinceForReportGroup($repgrp, $startDate, $endDate, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamUserLocByCAProvinceForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByLanguageForCPCode($cpcode, $date, $columns)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByLanguageForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamVisitorByLanguageForReportGroup($repgrp, $date, $columns)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');

		$result = $this->call("getVODStreamVisitorByLanguageForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamURLForCPCode($cpcodes, $startDate, $endDate, $columns, $sortColumn, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamURLForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamURLForReportGroup($repgrp, $startDate, $endDate, $columns, $sortColumn, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamURLForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamURLDailyAggregationForCPCode($cpcodes, $startDate, $endDate, $aggregateColumn, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["aggregateColumn"] = $this->parseParam($aggregateColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamURLDailyAggregationForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamURLDailyAggregationForReportGroup($repgrp, $startDate, $endDate, $aggregateColumn, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["aggregateColumn"] = $this->parseParam($aggregateColumn, 'xsd:string');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamURLDailyAggregationForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamSubdirForCPCode($cpcodes, $startDate, $endDate, $columns, $sortColumn, $level, $filter)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akasiteDeldt:ArrayOfInt');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["level"] = $this->parseParam($level, 'xsd:int');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamSubdirForCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODStreamSubdirForReportGroup($repgrp, $startDate, $endDate, $columns, $sortColumn, $level, $filter)
	{
		$params = array();
		
		$params["repgrp"] = $this->parseParam($repgrp, 'xsd:string');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:string');
		$params["columns"] = $this->parseParam($columns, 'akasiteDeldt:ArrayOfString');
		$params["sortColumn"] = $this->parseParam($sortColumn, 'xsd:string');
		$params["level"] = $this->parseParam($level, 'xsd:int');
		$params["filter"] = $this->parseParam($filter, 'akaaimsdt:UrlReportFilter');

		$result = $this->call("getVODStreamSubdirForReportGroup", $params);
		$this->logError();
		return $result;
	}
	
}		
	
