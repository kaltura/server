<?php
class KalturaClientBase 
{
	const KALTURA_API_VERSION = "3.0";
	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	/**
	 * @var KalturaConfiguration
	 */
	private $config;
	
	/**
	 * @var string
	 */
	private $ks;
	
	/**
	 * @var boolean
	 */
	private $shouldLog = false;
	
	/**
	 * Kaltura client constructor
	 *
	 */
	public function __construct(KalturaConfiguration $config)
	{
	    $this->config = $config;
	    
	    $logger = $this->config->getLogger();
		if ($logger)
		{
			$this->shouldLog = true;	
		}
	}

	public function callService($service, $action, $params)
	{
		$startTime = microtime(true);
		
		$this->log("service url: [" . $this->config->serviceUrl . "]");
		$this->log("trying to call service: [".$service.".".$action."] using session: [" .$this->ks . "]");
		
		// append the basic params
		$this->addParam($params, "apiVersion", self::KALTURA_API_VERSION);
		
		// in start session partner id is optional (default -1). if partner id was not set, use the one in the config
		if (!isset($params["partnerId"]) || $params["partnerId"] === -1)
	        $this->addParam($params, "partnerId", $this->config->partnerId);
	        
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "ks", $this->ks);

		$url = $this->config->serviceUrl."/api_v3/index.php?service=".$service."&action=".$action;
		$this->log("full reqeust url: [" . $url . "]");
		
		// flatten sub arrays (the objects)
		$newParams = array();
		foreach($params as $key => $val) 
		{
			if (is_array($val))
			{
				foreach($val as $subKey => $subVal)
				{
					$newParams[$key.":".$subKey] = $subVal;
				}
			}
			else
			{
				 $newParams[$key] = $val;
			}
		}

		$signature = $this->signature($newParams);
		$this->addParam($params, "kalsig", $signature);

		$this->log(print_r($newParams, true));
		
		list($postResult, $error) = $this->doHttpRequest($url, $newParams);

		if ($error)
		{
			throw new Exception($error);
		}
		else 
		{
			$this->log("result (serialized): " . $postResult);
			
			if ($this->config->format == self::KALTURA_SERVICE_FORMAT_PHP)
			{
				$result = @unserialize($postResult);

				if ($result === false && serialize(false) !== $postResult) 
				{
					throw new Exception("failed to serialize server result");
				}
				$dump = print_r($result, true);
				$this->log("result (object dump): " . $dump);
			}
			else
			{
				throw new Exception("unsupported format");
			}
		}
		
		$endTime = microtime (true);
		
		$this->log("execution time for service [".$service.".".$action."]: [" . ($endTime - $startTime) . "]");
		
		return $result;
	}

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
	
	private function doHttpRequest($url, $params, $optionalHeaders = null)
	{
		if (function_exists('curl_init'))
			return $this->doCurl($url, $params);
		else
			return $this->doPostRequest($url, $params, $optionalHeaders);
	}

	private function doCurl($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10 );

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlError);
	}

	private function doPostRequest($url, $data, $optionalHeaders = null)
	{
		$formattedData = http_build_query($data , "", "&");
		$params = array('http' => array(
					"method" => "POST",
					"Accept-language: en\r\n".
					"Content-type: application/x-www-form-urlencoded\r\n",
					"content" => $formattedData
		          ));
		if ($optionalHeaders !== null) {
		   $params['http']['header'] = $optionalHeaders;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			$phpErrorMsg = "";
			throw new Exception("Problem with $url, $phpErrorMsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
		   throw new Exception("Problem reading data from $url, $phpErrorMsg");
		}
		return array($response, '');
	}
		
	public function getKs()
	{
		return $this->ks;
	}
	
	public function setKs($ks)
	{
		$this->ks = $ks;
	}
	
	public function getConfig()
	{
		return $this->config;
	}
	
	public function setConfig(KalturaConfiguration $config)
	{
		$this->config = $config;
		
		$logger = $this->config->getLogger();
		if ($logger instanceof IKalturaLogger)
		{
			$this->shouldLog = true;	
		}
	}
	
	public function addParam(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			$params[$paramName] = $paramValue;
		}
	}
	
	public function throwExceptionIfError($resultObject)
	{
		if (is_array($resultObject) && isset($resultObject["message"]) && isset($resultObject["code"]))
		{
			throw new KalturaException($resultObject["message"], $resultObject["code"]);
		}
	}
	
	public function validateObjectType($resultObject, $objectType)
	{
		if (is_object($resultObject))
		{
			if (!($resultObject instanceof $objectType))
				throw new Exception("Invalid object type");
		}
		else if (gettype($resultObject) !== "NULL" && gettype($resultObject) !== $objectType)
		{
			throw new Exception("Invalid object type");
		}
	}
	
	protected function log($msg)
	{
		if ($this->shouldLog)
			$this->config->getLogger()->log($msg);
	}
}


/**
 * Abstract base class for all client services 
 *
 */
abstract class KalturaServiceBase
{
	protected $client;
	
	/**
	 * Initialize the service keeping reference to the KalturaClient
	 *
	 * @param KalturaClient $client
	 */
	public function __construct(KalturaClient $client)
	{
		$this->client = $client;
	}
}

/**
 * Abstract base class for all client objects 
 *
 */
abstract class KalturaObjectBase
{
	protected function addIfNotNull(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			$params[$paramName] = $paramValue;
		}
	}
	
	public function toParams()
	{
		$params = array();
		$params["objectType"] = get_class($this);
	    foreach($this as $prop => $val)
		{
			$this->addIfNotNull($params, $prop, $val);
		}
		return $params;
	}
}

class KalturaException extends Exception 
{
	protected $code;
	
    public function __construct($message, $code) 
    {
    	$this->code = $code;
		parent::__construct($message);
    }
}

class KalturaConfiguration
{
	private $logger;

	public $serviceUrl    = "http://www.kaltura.com/";
	public $partnerId     = null;
	public $format        = 3;
	
	/**
	 * Constructs new Kaltura configuration object
	 *
	 */
	public function __construct($partnerId)
	{
	    if (!is_numeric($partnerId))
	        throw new Exception("Invalid partner id");
	        
	    $this->partnerId = $partnerId;
	}
	
	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param IKalturaLogger $log
	 */
	public function setLogger(IKalturaLogger $log)
	{
		$this->logger = $log;
	}
	
	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return IKalturaLogger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}

/**
 * Implement to get kaltura client logs
 *
 */
interface IKalturaLogger 
{
	function log($msg); 
}


?>