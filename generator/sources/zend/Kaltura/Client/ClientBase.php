<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * Private class for null value
 */
class KalturaNull
{
	private static $instance;

	private function __construct()
	{

	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	function __toString()
	{
		return '';
	}


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class Kaltura_Client_ClientBase
{
	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	// KS V2 constants
	const RANDOM_SIZE = 16;

	const FIELD_EXPIRY =              '_e';
	const FIELD_TYPE =                '_t';
	const FIELD_USER =                '_u';

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
	 * @var array<string>
	 */
	private $responseHeaders = array();
	
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

	/* Store response headers into array */
	public function readHeader($ch, $string)
	{
		array_push($this->responseHeaders, $string);
		return strlen($string);
	}

	/* Retrive response headers */
	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}
	
	public function getServeUrl()
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
		if (!isset($params["partnerId"]))
			$params["partnerId"] = null;

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
		$this->addParam($params, "ignoreNull", true);

		$url = $this->config->serviceUrl."/api_v3/index.php?service=";
		if ($this->isMultiRequest)
		{
			$url .= "multirequest";
			$i = 1;
			foreach ($this->callsQueue as $call)
			{
				$callParams = $call->getParamsForMultiRequest($i);
				$callFiles = $call->getFilesForMultiRequest($i);
				$params = array_merge($params, $callParams);
				$files = array_merge($files, $callFiles);
				$i++;
			}
		}
		else
		{
			$call = $this->callsQueue[0];
			$url .= $call->service."&action=".$call->action;
			$params = array_merge($params, $call->params);
			$files = $call->files;
		}

		// reset
		$this->callsQueue = array();
		$this->isMultiRequest = false;

		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);

		list($postResult, $error) = $this->doHttpRequest($url, $params, $files);

		if ($error)
		{
			throw new Kaltura_Client_ClientException($error, Kaltura_Client_ClientException::ERROR_GENERIC);
		}
		else
		{
			// print server debug info to log
			$serverName = null;
			$serverSession = null;
			foreach ($this->responseHeaders as $curHeader)
			{
				$splittedHeader = explode(':', $curHeader, 2);
				if ($splittedHeader[0] == 'X-Me')
					$serverName = trim($splittedHeader[1]);
				else if ($splittedHeader[0] == 'X-Kaltura-Session')
					$serverSession = trim($splittedHeader[1]);
			}
			if (!is_null($serverName) || !is_null($serverSession))
				$this->log("server: [{$serverName}], session: [{$serverSession}]");
			
			$this->log("result (serialized): " . $postResult);

			if ($this->config->format == self::KALTURA_SERVICE_FORMAT_XML)
			{
				$result = $this->unmarshal($postResult);
				
				if ($result instanceof Kaltura_Client_Exception)
					throw $result;

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

		$endTime = microtime (true);

		$this->log("execution time for [".$url."]: [" . ($endTime - $startTime) . "]");

		return $result;
	}

	private static function unmarshalArray(SimpleXMLElement $xmls)
	{
		$ret = array();
		foreach($xmls as $xml)
			$ret[] = self::unmarshalItem($xml);

		return $ret;
	}

	public static function unmarshalItem(SimpleXMLElement $xml)
	{
		$nodeName = $xml->getName();

		if(!$xml->objectType)
		{
			if($xml->item)
				return self::unmarshalArray($xml->children());

			if($xml->error)
			{
				$code = "{$xml->error->code}";
				$message = "{$xml->error->message}";
				return new Kaltura_Client_Exception($message, $code);
			}

			return "$xml";
		}
		$objectType = reset($xml->objectType);

		$type = Kaltura_Client_TypeMap::getZendType($objectType);
		if(!class_exists($type))
			throw new Kaltura_Client_ClientException("Invalid object type class [$type] of Kaltura type [$objectType]", Kaltura_Client_ClientException::ERROR_INVALID_OBJECT_TYPE);

		return new $type($xml);
	}

	private function unmarshal($xmlData)
	{
		$xml = new SimpleXMLElement($xmlData);
		return self::unmarshalItem($xml->result);
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
		$this->responseHeaders = array();
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
		curl_setopt($ch, CURLOPT_USERAGENT, $this->config->userAgent);
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
        
		if (isset($this->config->proxyHost)) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $this->config->proxyHost);
			if (isset($this->config->proxyPort)) {
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->config->proxyPort);
			}
			if (isset($this->config->proxyUser)) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->proxyUser.':'.$this->config->proxyPassword);
			}
			if (isset($this->config->proxyType) && $this->config->proxyType === 'SOCKS5') {
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			}
		}

		if(!$this->config->verifySSL)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}
		elseif($this->config->sslCertificatePath)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_CAINFO, $this->config->sslCertificatePath);
		}

		// Set custom headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->config->requestHeaders );

		// Save response headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader') );
		
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
					"User-Agent: " . $this->config->userAgent . "\r\n".
					"Accept-language: en\r\n".
					"Content-type: application/x-www-form-urlencoded\r\n",
					"content" => $formattedData
				  ));

        if (isset($this->config->proxyType) && $this->config->proxyType === 'SOCKS5') {
			throw new Kaltura_Client_ClientException("Cannot use SOCKS5 without curl installed.", Kaltura_Client_ClientException::ERROR_CONNECTION_FAILED);
		}
		if (isset($this->config->proxyHost)) {
			$proxyhost = 'tcp://' . $this->config->proxyHost;
			if (isset($this->config->proxyPort)) {
				$proxyhost = $proxyhost . ":" . $this->config->proxyPort;
			}
			$params['http']['proxy'] = $proxyhost;
			$params['http']['request_fulluri'] = true;
			if (isset($this->config->proxyUser)) {
				$auth = base64_encode($this->config->proxyUser.':'.$this->config->proxyPassword);
				$params['http']['header'] = 'Proxy-Authorization: Basic ' . $auth;
			}
		}

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
	 * @return string
	 */
	public function getApiVersion()
	{
		return $this->apiVersion;
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

		if ($paramValue instanceof KalturaNull) {
			$params[$paramName . '__null'] = '';
			return;
		}

		if(is_object($paramValue) && $paramValue instanceof Kaltura_Client_ObjectBase)
		{
			$this->addParam($params, "$paramName:objectType", $paramValue->getKalturaObjectType());
			foreach($paramValue as $prop => $val)
				$this->addParam($params, "$paramName:$prop", $val);

			return;
		}

		if(!is_array($paramValue))
		{
			$params[$paramName] = (string)$paramValue;
			return;
		}

		if ($paramValue)
		{
			foreach($paramValue as $subParamName => $subParamValue)
				$this->addParam($params, "$paramName:$subParamName", $subParamValue);
		}
		else
		{
			$this->addParam($params, "$paramName:-", "");
		}
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
		else if( $objectType != 'string')
		{
			switch ($objectType)
			{
				case "integer":
					$resStringVal = strval(intval($resultObject));
					break;
				case "float":
					$resStringVal = strval(floatval($resultObject));
					break;
				default:
					$resStringVal = $resultObject;
			}
			if ($resStringVal != $resultObject)
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

	public function getMultiRequestQueueSize()
	{
		return count($this->callsQueue);
	}

    public function getMultiRequestResult()
	{
        return new Kaltura_Client_MultiRequestSubResult($this->getMultiRequestQueueSize() . ':result');
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

	public function generateSession($adminSecretForSigning, $userId, $type, $partnerId, $expiry = 86400, $privileges = '')
	{
		$rand = rand(0, 32000);
		$expiry = time()+$expiry;
		$fields = array (
			$partnerId ,
			$partnerId ,
			$expiry ,
			$type,
			$rand ,
			$userId ,
			$privileges
		);
		$info = implode ( ";" , $fields );

		$signature = $this->hash ( $adminSecretForSigning , $info );
		$strToHash =  $signature . "|" . $info ;
		$encoded_str = base64_encode( $strToHash );

		return $encoded_str;
	}

	public static function generateSessionV2($adminSecretForSigning, $userId, $type, $partnerId, $expiry, $privileges)
	{
		// build fields array
		$fields = array();
		foreach (explode(',', $privileges) as $privilege)
		{
			$privilege = trim($privilege);
			if (!$privilege)
				continue;
			if ($privilege == '*')
				$privilege = 'all:*';
			$splittedPrivilege = explode(':', $privilege, 2);
			if (count($splittedPrivilege) > 1)
				$fields[$splittedPrivilege[0]] = $splittedPrivilege[1];
			else
				$fields[$splittedPrivilege[0]] = '';
		}
		$fields[self::FIELD_EXPIRY] = time() + $expiry;
		$fields[self::FIELD_TYPE] = $type;
		$fields[self::FIELD_USER] = $userId;

		// build fields string
		$fieldsStr = http_build_query($fields, '', '&');
		$rand = '';
		for ($i = 0; $i < self::RANDOM_SIZE; $i++)
			$rand .= chr(rand(0, 0xff));
		$fieldsStr = $rand . $fieldsStr;
		$fieldsStr = sha1($fieldsStr, true) . $fieldsStr;

		// encrypt and encode
		$encryptedFields = self::aesEncrypt($adminSecretForSigning, $fieldsStr);
		$decodedKs = "v2|{$partnerId}|" . $encryptedFields;
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($decodedKs));
	}

	protected static function aesEncrypt($key, $message)
	{
		return mcrypt_encrypt(
			MCRYPT_RIJNDAEL_128,
			substr(sha1($key, true), 0, 16),
			$message,
			MCRYPT_MODE_CBC,
			str_repeat("\0", 16)	// no need for an IV since we add a random string to the message anyway
		);
	}

	private function hash ( $salt , $str )
	{
		return sha1($salt.$str);
	}

	/**
	 * @return KalturaNull
	 */
	public static function getKalturaNullValue()
	{
		return KalturaNull::getInstance();
	}
}
