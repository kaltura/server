<?php

require_once(__DIR__ . '/WebexClient.class.php');
require_once(__DIR__ . '/WebexRecordIdList.class.php');
	
class WebexXmlService extends WebexClient
{
	const WSDL_FILE = 'NBR_XML.wsdl';
	
	function __construct()
	{
		parent::__construct(__DIR__ . '/' . self::WSDL_FILE);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param string $ticket
	 * @return WebexXmlDocument
	 **/
	public function getNBRConfIdList($siteId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->doCall("getNBRConfIdList", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $ticket
	 * @return WebexXmlDocument
	 **/
	public function getMeetingXml($siteId, $confId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->doCall("getMeetingXml", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param string $userName
	 * @param string $password
	 * @return WebexRecordIdList
	 **/
	public function getNBRRecordIdList($siteId, $userName, $password)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["userName"] = $this->parseParam($userName, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');

		return $this->doCall("getNBRRecordIdList", $params, 'WebexRecordIdList');
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $ticket
	 * @return string
	 **/
	public function deleteMeetingXml($siteId, $confId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->doCall("deleteMeetingXml", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param string $username
	 * @param string $password
	 * @param string $service
	 * @return string
	 **/
	public function getMeetingTicket($siteId, $username, $password, $service)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["username"] = $this->parseParam($username, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');
		$params["service"] = $this->parseParam($service, 'xsd:string');

		return $this->doCall("getMeetingTicket", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $ticket
	 * @return WebexXmlDocument
	 **/
	public function getSCXml($siteId, $confId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->doCall("getSCXml", $params, 'WebexXmlDocument');
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $hashKey
	 * @return WebexXmlDocument
	 **/
	public function getSCXmlReport($siteId, $confId, $hashKey)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["hashKey"] = $this->parseParam($hashKey, 'xsd:string');

		return $this->doCall("getSCXmlReport", $params, 'WebexXmlDocument');
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $hashKey
	 * @return WebexXmlDocument
	 **/
	public function getNBRXmlForReport($siteId, $confId, $hashKey)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["hashKey"] = $this->parseParam($hashKey, 'xsd:string');

		return $this->doCall("getNBRXmlForReport", $params, 'WebexXmlDocument');
	}
	
}		
	
