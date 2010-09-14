<?php

	
class AkamaiLogRedeliveryServiceClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/webservices/services/LDSRedelivery?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function redeliverLogs($cpcodes, $serviceType, $date, $startTime, $endTime)
	{
		$params = array();
		
		$params["cpcodes"] = $this->parseParam($cpcodes, 'akaredelivery:ArrayOfInt');
		$params["serviceType"] = $this->parseParam($serviceType, 'xsd:string');
		$params["date"] = $this->parseParam($date, 'xsd:dateTime');
		$params["startTime"] = $this->parseParam($startTime, 'xsd:int');
		$params["endTime"] = $this->parseParam($endTime, 'xsd:int');

		$result = $this->call("redeliverLogs", $params);
		$this->logError();
		return $result;
	}
	
	public function getAvailableDates()
	{
		$params = array();
		

		$result = $this->call("getAvailableDates", $params);
		$this->logError();
		return $result;
	}
	
}		
	
