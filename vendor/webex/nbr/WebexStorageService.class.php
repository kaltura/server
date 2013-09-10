<?php

require_once(__DIR__ . '/WebexClient.class.php');
require_once(__DIR__ . '/WebexMaterialList.class.php');
	
class WebexStorageService extends WebexClient
{
	const WSDL_FILE = 'NBR_Storage.wsdl';
	
	function __construct()
	{
		parent::__construct(__DIR__ . '/' . self::WSDL_FILE);
	}

	/**
	 * 
	 * @param long $siteId
	 * @param string $username
	 * @param string $password
	 * @return string
	 **/
	public function getStorageAccessTicket($siteId, $username, $password)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["username"] = $this->parseParam($username, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');

		return $this->doCall("getStorageAccessTicket", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $ticket
	 * @param string $fileType
	 * @param dateTime $fromDate
	 * @param dateTime $toDate
	 * @return WebexMaterialList
	 **/
	public function getNBRStorageFile($siteId, $confId, $ticket, $fileType = null, $fromDate = null, $toDate = null)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');
		
		if($fileType)
			$params["fileType"] = $this->parseParam($fileType, 'xsd:string');
		if($fromDate)
			$params["fromDate"] = $this->parseParam($fromDate, 'xsd:dateTime');
		if($toDate)
			$params["toDate"] = $this->parseParam($toDate, 'xsd:dateTime');

		return $this->doCall("getNBRStorageFile", $params, 'WebexMaterialList');
	}
	
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $recordId
	 * @param string $ticket
	 * @return WebexFile
	 **/
	public function downloadNBRStorageFile($siteId, $recordId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["recordId"] = $this->parseParam($recordId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->download('downloadNBRStorageFile', $params);
	}

	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param long $recordId
	 * @param string $ticket
	 * @return string
	 **/
	public function deleteNBRStorageFile($siteId, $confId, $recordId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["recordId"] = $this->parseParam($recordId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->doCall("deleteNBRStorageFile", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $confId
	 * @param string $ticket
	 * @return WebexFile
	 **/
	public function downloadWAVFlie($siteId, $confId, $ticket)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["confId"] = $this->parseParam($confId, 'xsd:long');
		$params["ticket"] = $this->parseParam($ticket, 'xsd:string');

		return $this->download('downloadWAVFlie', $params);
	}
	
}		
	
