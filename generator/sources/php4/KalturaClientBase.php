<?php
define("KALTURA_API_VERSION", "3.0");
define("KALTURA_SERVICE_FORMAT_JSON", 1);
define("KALTURA_SERVICE_FORMAT_XML",  2);
define("KALTURA_SERVICE_FORMAT_PHP",  3);
	
class KalturaClientBase 
{
	/**
	 * @var KalturaConfiguration
	 */
	var $config;
	
	/**
	 * @var string
	 */
	var $ks;
	
	/**
	 * @var boolean
	 */
	var $shouldLog = false;
	
	/**
	 * @var string
	 */
	var $error;
	
	/**
	 * Kaltura client constuctor, expecting configuration object 
	 *
	 * @param KalturaConfiguration $config
	 */
	function KalturaClientBase(/*KalturaConfiguration*/ $config)
	{
		$this->config = $config;
		
		$logger = $this->config->getLogger();
		if (isset($logger))
		{
			$this->shouldLog = true;	
		}
	}

	function callService($service, $action, $params)
	{
		$startTime = microtime(true);
		$this->error = null;
		
		$this->log("service url: [" . $this->config->serviceUrl . "]");
		$this->log("trying to call service: [".$service.".".$action."] using session: [" .$this->ks . "]");
		
		// append the basic params
		$this->addParam($params, "apiVersion", KALTURA_API_VERSION);
		
		// in start session partner id is optional (default -1). if partner id was not set, use the one in the config
		if (!isset($params["partnerId"]) || $params["partnerId"] === -1)
	        $this->addParam($params, "partnerId", $this->config->partnerId);
    
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "clientTag", $this->config->clientTag);
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
			$this->setError(array("code" => 0, "message" => $error));
		}
		else 
		{
			$this->log("result (serialized): " . $postResult);
			
			if ($this->config->format == KALTURA_SERVICE_FORMAT_PHP)
			{
				$result = @unserialize($postResult);

				if ($result === false && serialize(false) !== $postResult) 
				{
					$this->setError(array("code" => 0, "message" => "failed to serialize server result"));
				}
				$dump = print_r($result, true);
				$this->log("result (object dump): " . $dump);
			}
			else
			{
				$this->setError(array("code" => 0, "message" => "unsupported format"));
			}
		}
		
		$endTime = microtime (true);
		
		$this->log("execution time for service [".$service.".".$action."]: [" . ($endTime - $startTime) . "]");
		
		return $result;
	}

	function signature($params)
	{
		ksort($params);
		$str = "";
		foreach ($params as $k => $v)
		{
			$str .= $k.$v;
		}
		return md5($str);
	}
	
	function doHttpRequest($url, $params, $optionalHeaders = null)
	{
		if (function_exists('curl_init'))
			return $this->doCurl($url, $params);
		else
			return $this->doPostRequest($url, $params, $optionalHeaders);
	}

	function doCurl($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10 );
		if (defined('CURLOPT_ENCODING'))
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlError);
	}

	function doPostRequest($url, $data, $optionalHeaders = null)
	{
		$formattedData = $this->httpParseQuery($data);
		
		if (!function_exists('fsockopen'))
		{
			$this->setError(array("code" => 0, "message" => "fsockopen is missing"));
			return;
		}
		$start = strpos($url,'//')+2;
		$end = strpos($url,'/',$start);
		$host = substr($url, $start, $end-$start);
		$domain = substr($url,$end);
		$fp = fsockopen($host, 80);
		if(!$fp) return null;
		fputs ($fp,"POST $domain HTTP/1.0\n"); // 1.0 beacause we don't want to support chunked transfer encoding
		fputs ($fp,"Host: $host\n");
		if ($optionalHeaders) {
			fputs($fp, $optionalHeaders);
		}
		fputs ($fp,"Content-type: application/x-www-form-urlencoded\n");
		fputs ($fp,"Content-length: ".strlen($formattedData)."\n\n");
		fputs ($fp,"$formattedData\n\n");
		
		$response = "";
		while(!feof($fp)) {
			$response .= fread($fp, 32768);
		}

		$pos = strpos($response, "\r\n\r\n");
		if ($pos)
			$response = substr($response, $pos + 4);
		else
			$response = "";
			
		fclose ($fp);
		return array($response, '');
	}
	
	function httpParseQuery($array = null, $convention = "%s")
	{
		if (!$array || count($array) == 0)
	        return ''; 
	        
		$query = ''; 
     
		foreach($array as $key => $value)
		{
		    if(is_array($value))
		    { 
				$new_convention = sprintf($convention, $key) . '[%s]'; 
			    $query .= $this->httpParseQuery($value, $new_convention); 
			} 
			else 
			{ 
			    $key = urlencode($key); 
			    $value = urlencode($value); 
         
			    $query .= sprintf($convention, $key) . "=$value&"; 
            } 
		} 
 
		return $query; 
	}
		
	function getKs()
	{
		return $this->ks;
	}
	
	function setKs($ks)
	{
		$this->ks = $ks;
	}
	
	function addParam(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			$params[$paramName] = $paramValue;
		}
	}
	
	function checkForError($resultObject)
	{
		if (is_array($resultObject) && isset($resultObject["message"]) && isset($resultObject["code"]))
		{
			$this->setError(array("code" => $resultObject["code"], "message" => $resultObject["message"]));
		}
	}
	
	function validateObjectType($resultObject, $objectType)
	{
		if (is_object($resultObject))
		{
			if (!(is_a($resultObject, $objectType)))
				$this->setError(array("code" => 0, "message" => "Invalid object type"));
		}
		else if (gettype($resultObject) !== "NULL" && gettype($resultObject) !== $objectType)
		{
			$this->setError(array("code" => 0, "message" => "Invalid object type"));
		}
	}
	
	function log($msg)
	{
		if ($this->shouldLog)
		{
		    $logger = $this->config->getLogger();
			$logger->log($msg);
		}
	}
	
	function setError($error)
	{
	    if ($this->error == null) // this is needed so only the first error will be set, and not the last
	    {
	        $this->error = $error;
	    }
	}
}


/**
 * Abstract base class for all client services 
 *
 */
class KalturaServiceBase
{
	var $client;
	
	/**
	 * Initialize the service keeping reference to the KalturaClient
	 *
	 * @param KalturaClient $client
	 */
	function KalturaServiceBase(/*KalturaClient*/ &$client)
	{
		$this->client = &$client;
	}
}

/**
 * Abstract base class for all client objects 
 *
 */
class KalturaObjectBase
{
	function addIfNotNull(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			$params[$paramName] = $paramValue;
		}
	}
		
	function toParams()
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

class KalturaConfiguration
{
	var $logger;

	var $serviceUrl    = "http://www.kaltura.com/";
	var $partnerId     = null;
	var $format        = 3;
	var $clientTag 	   = "php4";
	
	/**
	 * Constructs new Kaltura configuration object
	 *
	 */
	function KalturaConfiguration($partnerId)
	{
	    $this->partnerId = $partnerId;
	}
	
	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param IKalturaLogger $log
	 */
	function setLogger($log)
	{
		$this->logger = $log;
	}
	
	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return IKalturaLogger
	 */
	function getLogger()
	{
		return $this->logger;
	}
}
?>