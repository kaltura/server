<?php

require_once(__DIR__ . '/WebexClient.class.php');
	
class WebexFileService extends WebexClient
{
	const WSDL_FILE = 'NBR_File_Open.wsdl';
	
	function __construct()
	{
		parent::__construct(__DIR__ . '/' . self::WSDL_FILE);
	}

	/**
	 * 
	 * @param long $siteId
	 * @param string $serviceName
	 * @param string $userName
	 * @param string $password
	 * @param long $recordId
	 * @return WebexFileArrayOf_tns1_DataHandler
	 **/
	public function downloadFile($siteId, $serviceName, $userName, $password, $recordId)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["serviceName"] = $this->parseParam($serviceName, 'xsd:string');
		$params["userName"] = $this->parseParam($userName, 'xsd:string');
		$params["password"] = $this->parseParam($password, 'xsd:string');
		$params["recordId"] = $this->parseParam($recordId, 'xsd:long');

		return $this->doCall("downloadFile", $params);
	}
	
	/**
	 * 
	 * @param WebexFileDataHandler $dh
	 * @param long $siteId
	 * @param long $userId
	 * @param string $recordName
	 * @param string $fileType
	 * @param long $filesize
	 * @param long $duration
	 * @param int $serviceType
	 * @param string $appToken
	 * @return long
	 **/
	public function uploadRecording(WebexFileDataHandler $dh, $siteId, $userId, $recordName, $fileType, $filesize, $duration, $serviceType, $appToken)
	{
		$params = array();
		
		$params["dh"] = $this->parseParam($dh, 'tns1:DataHandler');
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["userId"] = $this->parseParam($userId, 'xsd:long');
		$params["recordName"] = $this->parseParam($recordName, 'xsd:string');
		$params["fileType"] = $this->parseParam($fileType, 'xsd:string');
		$params["filesize"] = $this->parseParam($filesize, 'xsd:long');
		$params["duration"] = $this->parseParam($duration, 'xsd:long');
		$params["serviceType"] = $this->parseParam($serviceType, 'xsd:int');
		$params["appToken"] = $this->parseParam($appToken, 'xsd:string');

		return $this->doCall("uploadRecording", $params);
	}
	
	/**
	 * 
	 * @param long $siteId
	 * @param long $recordId
	 * @param string $appToken
	 * @param boolean $deleteRomoteFile
	 * @return boolean
	 **/
	public function deleteUploadedRecording($siteId, $recordId, $appToken, $deleteRomoteFile)
	{
		$params = array();
		
		$params["siteId"] = $this->parseParam($siteId, 'xsd:long');
		$params["recordId"] = $this->parseParam($recordId, 'xsd:long');
		$params["appToken"] = $this->parseParam($appToken, 'xsd:string');
		$params["deleteRomoteFile"] = $this->parseParam($deleteRomoteFile, 'xsd:boolean');

		return $this->doCall("deleteUploadedRecording", $params);
	}
	
}		
	
