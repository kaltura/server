<?php

class ComcastClient extends nusoap_client
{
	const PARAM_TYPE_TIMESTAMP = 'xsd:dateTime';
	
	function __construct($wsdlUrl, $username, $password)
	{
		$this->authtype = 'basic';
		$this->username = $username;
		$this->password = $password;
		
		parent::__construct($wsdlUrl, 'wsdl');
	}

	function parseParam($value, $type = null)
	{
		if($type == self::PARAM_TYPE_TIMESTAMP)
		{
			if(is_null($value))
				return null;
				
			return timestamp_to_iso8601($value);
		}
			
		if(is_null($value))
			return 'Null';
			
		return $value;
	}

	function logError()
	{
		if ($this->getError())
		{
			echo("ComcastClient error calling operation: [".$this->operation."], error: [".$this->getError()."], request: [".$this->request."], response: [".$this->response."]");
			//KalturaLog::err("ComcastClient error calling operation: [".$this->operation."], error: [".$this->getError()."], request: [".$this->request."], response: [".$this->response."]");
		}
	}
	
	/**
	 * @param string $operation
	 * @param array $params
	 * @param string $returnedType
	 */
	function doCall($operation, array $params = array(), $returnedType = null)
	{
		$result = $this->call($operation, $params);
		if(!$result)
			return false;
			
		if(!$returnedType)
			return $returnedType;
			
		$return = new $returnedType();
		$return->fromArray($result);
		return $return;
	}
}
