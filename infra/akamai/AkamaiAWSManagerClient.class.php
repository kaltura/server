<?php

	
class AkamaiAWSManagerClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/webservices/services/AWSManager?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function getUserInfo()
	{
		$params = array();
		

		$result = $this->call("getUserInfo", $params);
		$this->logError();
		return $result;
	}
	
	public function getPasswordExpirationDate()
	{
		$params = array();
		

		$result = $this->call("getPasswordExpirationDate", $params);
		$this->logError();
		return $result;
	}
	
	public function updateUserPassword($oldPassword, $newPassword, $verifyNewPassword)
	{
		$params = array();
		
		$params["oldPassword"] = $this->parseParam($oldPassword, 'xsd:string');
		$params["newPassword"] = $this->parseParam($newPassword, 'xsd:string');
		$params["verifyNewPassword"] = $this->parseParam($verifyNewPassword, 'xsd:string');

		$result = $this->call("updateUserPassword", $params);
		$this->logError();
		return $result;
	}
	
	public function getAccountNamesForIds($accountIds)
	{
		$params = array();
		
		$params["accountIds"] = $this->parseParam($accountIds, 'akaawsmgrdt:ArrayOfString');

		$result = $this->call("getAccountNamesForIds", $params);
		$this->logError();
		return $result;
	}
	
	public function getAccountIdsForNames($accountIds)
	{
		$params = array();
		
		$params["accountIds"] = $this->parseParam($accountIds, 'akaawsmgrdt:ArrayOfString');

		$result = $this->call("getAccountIdsForNames", $params);
		$this->logError();
		return $result;
	}
	
}		
	
