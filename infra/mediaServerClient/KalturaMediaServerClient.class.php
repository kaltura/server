<?php
require_once( __DIR__ . '/KalturaMediaServerClientException.class.php');

/**
 * @package External
 * @subpackage WSDL
 */
class KalturaMediaServerClient extends nusoap_client
{
	const PARAM_TYPE_TIMESTAMP = 'xsd:dateTime';
	
	function __construct($wsdlUrl, $username = null, $password = null)
	{
		$this->authtype = 'basic';
		$this->username = $username;
		$this->password = $password;
		
		parent::__construct($wsdlUrl, 'wsdl');
		$this->setVerbose(false);
	}

	public function setVerbose($verbose)
	{
		if($verbose)
		{
			$this->setDebugLevel(9);
			$this->setCurlOption(CURLOPT_VERBOSE, true);
		}
		else
		{
			$this->setDebugLevel(0);
			$this->setCurlOption(CURLOPT_VERBOSE, false);
		}
	}

	function appendDebug($string)
	{
		if ($this->debugLevel > 0)
			echo $string;

		return parent::appendDebug($string);
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

	public function doCall($operation, array $params = array(), $type = null)
	{
		$result = $this->call($operation, $params);
		$this->throwError();
		
		if($type)
			return new $type($result);
			
		return $result;
	}
	
	function throwError()
	{
		if ($this->getError())
			throw new KalturaMediaServerClientException("KalturaMediaServerClient error calling operation: [".$this->operation."], error: [".$this->getError()."], request: [".$this->request."], response: [".$this->response."]");
	}
}
