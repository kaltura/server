<?php
/**
 * @package External
 * @subpackage Akamai
 */
class AkamaiBillingReportsClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/nmrws/services/BillingReports?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function getUsageReport($datasources, $startDate)
	{
		$params = array();
		
		$params["datasources"] = $this->parseParam($datasources, 'akaaurdt:ArrayOfString');
		$params["startDate"] = $this->parseParam($startDate, 'xsd:string');

		$result = $this->call("getUsageReport", $params);
		$this->logError();
		return $result;
	}
	
	public function getContracts()
	{
		$params = array();
		

		$result = $this->call("getContracts", $params);
		$this->logError();
		return $result;
	}
	
	public function getReportingGroups()
	{
		$params = array();
		

		$result = $this->call("getReportingGroups", $params);
		$this->logError();
		return $result;
	}
	
}		
	
