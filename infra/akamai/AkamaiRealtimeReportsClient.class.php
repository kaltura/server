<?php

	
class AkamaiRealtimeReportsClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/nmrws/services/RealtimeReports?wsdl';
	
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
	
	public function getFreeFlowSummary($cpcodes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');

		$result = $this->call("getFreeFlowSummary", $params);
		$this->logError();
		return $result;
	}
	
	public function getFreeFlowSummaryByCPCode($cpcodes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');

		$result = $this->call("getFreeFlowSummaryByCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getFreeFlowGeotable($cpcodes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');

		$result = $this->call("getFreeFlowGeotable", $params);
		$this->logError();
		return $result;
	}
	
	public function getStreamingSummary($cpcodes, $mediaTypes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["mediaTypes"] = $this->parseParam($mediaTypes, 'akaarrdt:ArrayOfString');

		$result = $this->call("getStreamingSummary", $params);
		$this->logError();
		return $result;
	}
	
	public function getStreamingSummaryByCPCode($cpcodes, $mediaTypes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["mediaTypes"] = $this->parseParam($mediaTypes, 'akaarrdt:ArrayOfString');

		$result = $this->call("getStreamingSummaryByCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getStreamingGeotable($cpcodes, $mediaTypes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["mediaTypes"] = $this->parseParam($mediaTypes, 'akaarrdt:ArrayOfString');

		$result = $this->call("getStreamingGeotable", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeSuiteSummary($cpcodes, $network, $contentType)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["network"] = $this->parseParam($network, 'xsd:string');
		$params["contentType"] = $this->parseParam($contentType, 'xsd:string');

		$result = $this->call("getEdgeSuiteSummary", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeSuiteSummaryByCPCode($cpcodes, $network, $contentType)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["network"] = $this->parseParam($network, 'xsd:string');
		$params["contentType"] = $this->parseParam($contentType, 'xsd:string');

		$result = $this->call("getEdgeSuiteSummaryByCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeSuiteGeotable($cpcodes, $network, $contentType)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');
		$params["network"] = $this->parseParam($network, 'xsd:string');
		$params["contentType"] = $this->parseParam($contentType, 'xsd:string');

		$result = $this->call("getEdgeSuiteGeotable", $params);
		$this->logError();
		return $result;
	}
	
	public function getContentStorageSummary($cpcodes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');

		$result = $this->call("getContentStorageSummary", $params);
		$this->logError();
		return $result;
	}
	
	public function getContentStorageSummaryByCPCode($cpcodes)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaarrdt:ArrayOfInt');

		$result = $this->call("getContentStorageSummaryByCPCode", $params);
		$this->logError();
		return $result;
	}
	
	public function getFirstPointSummary($domains)
	{
		$params = array();
		
		$params["domains"] = $this->parseParam($domains, 'akaarrdt:ArrayOfString');

		$result = $this->call("getFirstPointSummary", $params);
		$this->logError();
		return $result;
	}
	
	public function getFirstPointGeotable($domains)
	{
		$params = array();
		
		$params["domains"] = $this->parseParam($domains, 'akaarrdt:ArrayOfString');

		$result = $this->call("getFirstPointGeotable", $params);
		$this->logError();
		return $result;
	}
	
	public function getFirstPointIPStatus($domains)
	{
		$params = array();
		
		$params["domains"] = $this->parseParam($domains, 'akaarrdt:ArrayOfString');

		$result = $this->call("getFirstPointIPStatus", $params);
		$this->logError();
		return $result;
	}
	
	public function getFirstPointDataCenter($domains)
	{
		$params = array();
		
		$params["domains"] = $this->parseParam($domains, 'akaarrdt:ArrayOfString');

		$result = $this->call("getFirstPointDataCenter", $params);
		$this->logError();
		return $result;
	}
	
	public function getActiveAlerts()
	{
		$params = array();
		

		$result = $this->call("getActiveAlerts", $params);
		$this->logError();
		return $result;
	}
	
	public function getActiveFailLivenessCheckAlerts()
	{
		$params = array();
		

		$result = $this->call("getActiveFailLivenessCheckAlerts", $params);
		$this->logError();
		return $result;
	}
	
	public function getActiveAlertsDetails()
	{
		$params = array();
		

		$result = $this->call("getActiveAlertsDetails", $params);
		$this->logError();
		return $result;
	}
	
}		
	
