<?php
/**
 * @package External
 * @subpackage Akamai
 */
class AkamaiEventDataClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/nmrws/services/EventData?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function getEdgeControlEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:dateTime');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:dateTime');

		$result = $this->call("getEdgeControlEvents", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeControlAlertsEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:dateTime');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:dateTime');

		$result = $this->call("getEdgeControlAlertsEvents", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeControlManageEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:dateTime');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:dateTime');

		$result = $this->call("getEdgeControlManageEvents", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeControlReportsEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:dateTime');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:dateTime');

		$result = $this->call("getEdgeControlReportsEvents", $params);
		$this->logError();
		return $result;
	}
	
	public function getEdgeControlSupportEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:dateTime');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:dateTime');

		$result = $this->call("getEdgeControlSupportEvents", $params);
		$this->logError();
		return $result;
	}
	
	public function getEvents($startTime, $stopTime)
	{
		$params = array();
		
		$params["startTime"] = $this->parseParam($startTime, 'xsd:string');
		$params["stopTime"] = $this->parseParam($stopTime, 'xsd:string');

		$result = $this->call("getEvents", $params);
		$this->logError();
		return $result;
	}
	
}		
	
