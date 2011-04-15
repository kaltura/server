<?php
/**
 * @package Kaltura
 * @subpackage Client
 */
class Kaltura_Client_ClientBase 
{
	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	/**
	 * @var string
	 */
	protected $apiVersion = null;

	/**
	 * @var Kaltura_Client_Configuration
	 */
	protected $config;
	
	/**
	 * @var string
	 */
	private $ks;
	
	/**
	 * @var boolean
	 */
	private $shouldLog = false;
	
	/**
	 * @var bool
	 */
	private $isMultiRequest = false;
	
	/**
	 * @var array<Kaltura_Client_ServiceActionCall>
	 */
	private $callsQueue = array();

	/**
	 * Kaltura client constructor
	 *
	 * @param Kaltura_Client_Configuration $config
	 */
	public function __construct(Kaltura_Client_Configuration $config)
	{
	    $this->config = $config;
	    
	    $logger = $this->config->getLogger();
		if ($logger)
			$this->shouldLog = true;	
	}

	public function getServeUrl($service, $action, array $params = null)
	{
		if (count($this->callsQueue) != 1)
			return null;
			 
		$params = array();
		$files = array();
		$this->log("service url: [" . $this->config->serviceUrl . "]");
		
		// append the basic params
		$this->addParam($params, "apiVersion", $this->apiVersion);
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "clientTag", $this->config->clientTag);
		
		$call = $this->callsQueue[0];
		$this->callsQueue = array();
		$this->isMultiRequest = false; 
		
		$params = array_merge($params, $call->params);
		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);
		
		$url = $this->config->serviceUrl . "/api_v3/index.php?service={$call->service}&action={$call->action}";
		$url .= '&' . http_build_query($params); 
		$this->log("Returned url [$url]");
		return $url;
	}

	public function queueServiceActionCall($service, $action, $params = array(), $files = array())
	{
		// in start session partner id is optional (default -1). if partner id was not set, use the one in the config
		if (!isset($params["partnerId"]) || $params["partnerId"] === -1)
			$params["partnerId"] = $this->config->partnerId;
			
		$this->addParam($params, "ks", $this->ks);
		
		$call = new Kaltura_Client_ServiceActionCall($service, $action, $params, $files);
		$this->callsQueue[] = $call;
	}
	
	/**
	 * Call all API service that are in queue
	 *
	 * @return unknown
	 */
	public function doQueue()
	{
		if (count($this->callsQueue) == 0)
		{
			$this->isMultiRequest = false; 
			return null;
		}
			 
		$startTime = microtime(true);
				
		$params = array();
		$files = array();
		$this->log("service url: [" . $this->config->serviceUrl . "]");
		
		// append the basic params
		$this->addParam($params, "apiVersion", $this->apiVersion);
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "clientTag", $this->config->clientTag);
		
		$url = $this->config->serviceUrl."/api_v3/index.php?service=";
		if ($this->isMultiRequest)
		{
			$url .= "multirequest";
			$i = 1;
			foreach ($this->callsQueue as $call)
			{
				$callParams = $call->getParamsForMultiRequest($i++);
				$params = array_merge($params, $callParams);
				$files = array_merge($files, $call->files);
			}
		}
		else
		{
			$call = $this->callsQueue[0];
			$url .= $call->service."&action=".$call->action;
			$params = array_merge($params, $call->params);
			$files = $call->files;
		}
		
		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);
		
		list($postResult, $error) = $this->doHttpRequest($url, $params, $files);
		
		if ($error)
		{
			throw new Kaltura_Client_ClientException($error, Kaltura_Client_ClientException::ERROR_GENERIC);
		}
		else 
		{
//			if(strlen($postResult) > 8192)
//				$this->log("result (serialized): " . strlen($postResult) . " bytes");
//			else
				$this->log("result (serialized): " . $postResult);
			
			if ($this->config->format == self::KALTURA_SERVICE_FORMAT_XML)
			{
				$result = $this->unmarshal($postResult);

				if (is_null($result)) 
					throw new Kaltura_Client_ClientException("failed to unserialize server result\n$postResult", Kaltura_Client_ClientException::ERROR_UNSERIALIZE_FAILED);
					
				$dump = print_r($result, true);
//				if(strlen($dump) < 8192)
					$this->log("result (object dump): " . $dump);
			}
			else
			{
				throw new Kaltura_Client_ClientException("unsupported format: $postResult", Kaltura_Client_ClientException::ERROR_FORMAT_NOT_SUPPORTED);
			}
		}
		
		// reset
		$this->callsQueue = array();
		$this->isMultiRequest = false; 
		
		$endTime = microtime (true);
		
		$this->log("execution time for [".$url."]: [" . ($endTime - $startTime) . "]");
		
		return $result;
	}

	private function unmarshalArray(SimpleXMLElement $xmls)
	{
		$ret = array();
		foreach($xmls as $xml)
			$ret[] = $this->unmarshalItem($xml);
			
		return $ret;
	}

	private function unmarshalItem(SimpleXMLElement $xml)
	{
		$nodeName = $xml->getName();
		
		if(!$xml->objectType)
		{
			if($xml->item)
				return $this->unmarshalArray($xml->children());
				
			if($xml->error)
			{
				$code = "{$xml->error->code}";
				$message = "{$xml->erroe->message}";
				throw new Kaltura_Client_Exception($message, $code);
			}
			
			return "$xml";
		}
		$objectType = reset($xml->objectType);
			
		$type = Kaltura_Client_TypeMap::getZendType($objectType);
		$this->log("Instantiating new object type [$type] server type [$objectType]");
		$ret = new $type();
	
		foreach($xml->children() as $attributeName => $attributeValue)
		{
			if($attributeName == 'objectType')
				continue;
				
			$ret->$attributeName = $this->unmarshalItem($attributeValue);
		}
		
		return $ret;
	}

	private function unmarshal($xmlData)
	{
		$xml = new SimpleXMLElement($xmlData);
		return $this->unmarshalItem($xml->result);
	}

	/**
	 * Sign array of parameters
	 *
	 * @param array $params
	 * @return string
	 */
	private function signature($params)
	{
		ksort($params);
		$str = "";
		foreach ($params as $k => $v)
		{
			$str .= $k.$v;
		}
		return md5($str);
	}
	
	/**
	 * Send http request by using curl (if available) or php stream_context
	 *
	 * @param string $url
	 * @param parameters $params
	 * @return array of result and error
	 */
	private function doHttpRequest($url, $params = array(), $files = array())
	{
		if (function_exists('curl_init'))
			return $this->doCurl($url, $params, $files);
		else
			return $this->doPostRequest($url, $params, $files);
	}

	/**
	 * Curl HTTP POST Request
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result and error
	 */
	private function doCurl($url, $params = array(), $files = array())
	{
		$cookies = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		if (count($files) > 0)
		{
			foreach($files as &$file)
				$file = "@".$file; // let curl know its a file
			curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($params, $files));
		}
		else
		{
			$opt = http_build_query($params, null, "&");
			$this->log("curl: $url&$opt");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		if (count($files) > 0)
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		else
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->curlTimeout);
			
		if ($this->config->startZendDebuggerSession === true)
		{
			$zendDebuggerParams = $this->getZendDebuggerParams($url);
		 	$cookies = array_merge($cookies, $zendDebuggerParams);
		}
		
		if (count($cookies) > 0) 
		{
			$cookiesStr = http_build_query($cookies, null, '; ');
			curl_setopt($ch, CURLOPT_COOKIE, $cookiesStr);
		} 

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlError);
	}

	/**
	 * HTTP stream context request 
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result and error
	 */
	private function doPostRequest($url, $params = array(), $files = array())
	{
		if (count($files) > 0)
			throw new Kaltura_Client_ClientException("Uploading files is not supported with stream context http request, please use curl", Kaltura_Client_ClientException::ERROR_UPLOAD_NOT_SUPPORTED);
			
		$formattedData = http_build_query($params , "", "&");
		$params = array('http' => array(
					"method" => "POST",
					"Accept-language: en\r\n".
					"Content-type: application/x-www-form-urlencoded\r\n",
					"content" => $formattedData
		          ));

		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			$phpErrorMsg = "";
			throw new Kaltura_Client_ClientException("Problem with $url, $phpErrorMsg", Kaltura_Client_ClientException::ERROR_CONNECTION_FAILED);
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
		   throw new Kaltura_Client_ClientException("Problem reading data from $url, $phpErrorMsg", Kaltura_Client_ClientException::ERROR_READ_FAILED);
		}
		return array($response, '');
	}

	/**
	 * @return string
	 */
	public function getKs()
	{
		return $this->ks;
	}
	
	/**
	 * @param string $ks
	 */
	public function setKs($ks)
	{
		$this->ks = $ks;
	}
	
	/**
	 * @return Kaltura_Client_Configuration
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	/**
	 * @param Kaltura_Client_Configuration $config
	 */
	public function setConfig(Kaltura_Client_Configuration $config)
	{
		$this->config = $config;
		
		$logger = $this->config->getLogger();
		if ($logger instanceof Kaltura_Client_ILogger)
		{
			$this->shouldLog = true;	
		}
	}
	
	/**
	 * Add parameter to array of parameters that is passed by reference
	 *
	 * @param arrat $params
	 * @param string $paramName
	 * @param string $paramValue
	 */
	public function addParam(&$params, $paramName, $paramValue)
	{
		if ($paramValue === null)
			return;
			
		if(is_object($paramValue) && $paramValue instanceof Kaltura_Client_ObjectBase)
		{
			$this->addParam($params, "$paramName:objectType", $paramValue->getKalturaObjectType());
		    foreach($paramValue as $prop => $val)
				$this->addParam($params, "$paramName:$prop", $val);
				
			return;
		}	
		
		if(!is_array($paramValue))
		{
			$params[$paramName] = $paramValue;
			return;
		}
		
		foreach($paramValue as $subParamName => $subParamValue)
			$this->addParam($params, "$paramName:$subParamName", $subParamValue);
	}
	
	/**
	 * Validate the result object and throw exception if its an error
	 *
	 * @param object $resultObject
	 */
	public function throwExceptionIfError($resultObject)
	{
		if ($this->isError($resultObject))
		{
			throw new Kaltura_Client_Exception($resultObject["message"], $resultObject["code"]);
		}
	}
	
	/**
	 * Checks whether the result object is an error
	 *
	 * @param object $resultObject
	 */
	public function isError($resultObject)
	{
		return (is_array($resultObject) && isset($resultObject["message"]) && isset($resultObject["code"]));
	}
	
	/**
	 * Validate that the passed object type is of the expected type
	 *
	 * @param any $resultObject
	 * @param string $objectType
	 */
	public function validateObjectType($resultObject, $objectType)
	{
		if (is_object($resultObject))
		{
			if (!($resultObject instanceof $objectType))
				throw new Kaltura_Client_ClientException("Invalid object type", Kaltura_Client_ClientException::ERROR_INVALID_OBJECT_TYPE);
		}
		else if (gettype($resultObject) != "NULL" && gettype($resultObject) != $objectType)
		{
			throw new Kaltura_Client_ClientException("Invalid object type [" . gettype($resultObject) . "] expected [$objectType]", Kaltura_Client_ClientException::ERROR_INVALID_OBJECT_TYPE);
		}
	}
	
	public function startMultiRequest()
	{
		$this->isMultiRequest = true;
	}
	
	public function doMultiRequest()
	{
		return $this->doQueue();
	}
	
	public function isMultiRequest()
	{
		return $this->isMultiRequest;	
	}
	
	/**
	 * @param string $msg
	 */
	protected function log($msg)
	{
		if ($this->shouldLog)
			$this->config->getLogger()->log($msg);
	}
	
	/**
	 * Return a list of parameter used to a new start debug on the destination server api
	 * @link http://kb.zend.com/index.php?View=entry&EntryID=434
	 * @param $url
	 */
	protected function getZendDebuggerParams($url)
	{
		$params = array();
		$passThruParams = array('debug_host',
			'debug_fastfile',
			'debug_port',
			'start_debug',
			'send_debug_header',
			'send_sess_end',
			'debug_jit',
			'debug_stop',
			'use_remote');
		
		foreach($passThruParams as $param)
		{
			if (isset($_COOKIE[$param]))
				$params[$param] = $_COOKIE[$param];
		}
		
		$params['original_url'] = $url;
		$params['debug_session_id'] = microtime(true); // to create a new debug session
		
		return $params;
	}
}
