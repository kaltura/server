<?php
// ===================================================================================================
//						   _  __	 _ _
//						  | |/ /__ _| | |_ _  _ _ _ __ _
//						  | ' </ _` | |  _| || | '_/ _` |
//						  |_|\_\__,_|_|\__|\_,_|_| \__,_|
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
 * @namespace
 */
namespace Kaltura\Client;

/**
 * @package Kaltura
 * @subpackage Client
 */
class Base
{
	// KS V2 constants
	const RANDOM_SIZE = 16;

	const FIELD_EXPIRY =			  '_e';
	const FIELD_TYPE =				'_t';
	const FIELD_USER =				'_u';

	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	/**
	 * @var \Kaltura\Client\Configuration
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $clientConfiguration = array();

	/**
	 * @var array
	 */
	protected $requestConfiguration = array();

	/**
	 * @var boolean
	 */
	private $shouldLog = false;

	/**
	 * @var Array of classes
	 */
	private $multiRequestReturnType = null;

	/**
	 * @var array<\Kaltura\Client\ServiceActionCall>
	 */
	private $callsQueue = array();

	/**
	* @var Array of response headers
	*/
	private $responseHeaders = array();

	/**
	 * Kaltura client constructor
	 *
	 * @param \Kaltura\Client\Configuration $config
	 */
	public function __construct(Configuration $config)
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

	/* Retrieve response headers */
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
		$this->log("service url: [" . $this->config->getServiceUrl() . "]");

		// append the basic params
		$this->addParam($params, "format", $this->config->getFormat());

		foreach($this->clientConfiguration as $param => $value)
		{
			$this->addParam($params, $param, $value);
		}

		$call = $this->callsQueue[0];
		$this->callsQueue = array();

		$params = array_merge($params, $call->params);
		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);

		$url = $this->config->getServiceUrl() . "/api_v3/index.php?service={$call->service}&action={$call->action}";
		$url .= '&' . http_build_query($params);
		$this->log("Returned url [$url]");
		return $url;
	}

	public function queueServiceActionCall($service, $action, $returnType, $params = array(), $files = array())
	{
		foreach($this->requestConfiguration as $param => $value)
		{
			$this->addParam($params, $param, $value);
		}

		$call = new ServiceActionCall($service, $action, $params, $files);
		if(!is_null($this->multiRequestReturnType))
			$this->multiRequestReturnType[] = $returnType;
		$this->callsQueue[] = $call;
	}

	protected function resetRequest()
	{
		$this->multiRequestReturnType = null;
		$this->callsQueue = array();
	}

	/**
	 * Call all API service that are in queue
	 *
	 * @return object
	 */
	public function doQueue()
	{
		if (count($this->callsQueue) == 0)
		{
			$this->resetRequest();
			return null;
		}

		$startTime = microtime(true);

		$params = array();
		$files = array();
		$this->log("service url: [" . $this->config->getServiceUrl() . "]");

		// append the basic params
		$this->addParam($params, "format", $this->config->getFormat());
		$this->addParam($params, "ignoreNull", true);

		foreach($this->clientConfiguration as $param => $value)
		{
			$this->addParam($params, $param, $value);
		}

		$url = $this->config->getServiceUrl()."/api_v3/index.php?service=";
		if (!is_null($this->multiRequestReturnType))
		{
			$url .= "multirequest";
			$i = 0;
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

		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);

		list($postResult, $errorCode, $error) = $this->doHttpRequest($url, $params, $files);

		if ($error || ($errorCode != 200 ))
		{
			$error .= ". RC : $errorCode";
			$this->resetRequest();
			throw new ClientException($error, ClientException::ERROR_GENERIC);
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

			if ($this->config->getFormat() != self::KALTURA_SERVICE_FORMAT_XML)
			{
				$this->resetRequest();
				throw new ClientException("unsupported format: $postResult", ClientException::ERROR_FORMAT_NOT_SUPPORTED);
			}
		}
		
		$this->resetRequest();

		$endTime = microtime (true);

		$this->log("execution time for [".$url."]: [" . ($endTime - $startTime) . "]");

		return $postResult;
	}

	/**
	 * Sorts array recursively
	 *
	 * @param array $params
	 * @param int $flags
	 * @return boolean
	 */
	protected function ksortRecursive(&$array, $flags = null) 
	{
		ksort($array, $flags);
		foreach($array as &$arr) {
			if(is_array($arr))
				$this->ksortRecursive($arr, $flags);
		}
		return true;
	}

	/**
	 * Sign array of parameters
	 *
	 * @param array $params
	 * @return string
	 */
	private function signature($params)
	{
		$this->ksortRecursive($params);
		return md5($this->jsonEncode($params));
	}

	/**
	 * Send http request by using curl (if available) or php stream_context
	 *
	 * @param string $url
	 * @param parameters $params
	 * @return array of result, error code and error
	 */
	private function doHttpRequest($url, $params = array(), $files = array())
	{
		if (!function_exists('curl_init'))
			throw new ClientException("Curl extension must be enabled", ClientException::ERROR_CURL_MUST_BE_ENABLED);

		return $this->doCurl($url, $params, $files);
	}

	/**
	 * Curl HTTP POST Request
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result, error code and error
	 */
	private function doCurl($url, $params = array(), $files = array())
	{
		$this->responseHeaders = array();
		$requestHeaders = $this->config->getRequestHeaders();
		
		$params = $this->jsonEncode($params);
		$this->log("curl: $url");
		$this->log("post: $params");
		if($this->config->getFormat() == self::KALTURA_SERVICE_FORMAT_JSON)
		{
			$requestHeaders[] = 'Accept: application/json';
		}
		
		$cookies = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		if (count($files) > 0)
		{
			$params = array('json' => $params);
			foreach ($files as $key => $file) {
				// The usage of the @filename API for file uploading is
				// deprecated since PHP 5.5. CURLFile must be used instead.
				if (PHP_VERSION_ID >= 50500) {
					$params[$key] = new \CURLFile($file);
				} else {
					$params[$key] = "@" . $file; // let curl know its a file
				}
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		else
		{
			$requestHeaders[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->config->getUserAgent());
		if (count($files) > 0)
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		else
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->getCurlTimeout());

		if ($this->config->getStartZendDebuggerSession() === true)
		{
			$zendDebuggerParams = $this->getZendDebuggerParams($url);
			$cookies = array_merge($cookies, $zendDebuggerParams);
		}

		if (count($cookies) > 0)
		{
			$cookiesStr = http_build_query($cookies, null, '; ');
			curl_setopt($ch, CURLOPT_COOKIE, $cookiesStr);
		}

		if ($this->config->getProxyHost()) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $this->config->getProxyHost());
			if ($this->config->getProxyPort()) {
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->config->getProxyPort());
			}
			if ($this->config->getProxyUser()) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->getProxyUser().':'.$this->config->getProxyPassword());
			}
			if ($this->config->getProxyType() === 'SOCKS5') {
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			}
		}

		// Set SSL verification
		if(!$this->config->getVerifySSL())
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}

		// Set custom headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

		// Save response headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader') );

		$result = curl_exec($ch);
		$curlErrorCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlErrorCode, $curlError);
	}

	/**
	 * @return Configuration
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param Configuration $config
	 */
	public function setConfig(Configuration $config)
	{
		$this->config = $config;

		$logger = $this->config->getLogger();
		if ($logger instanceof ILogger)
		{
			$this->shouldLog = true;
		}
	}

	public function setClientConfiguration(\Kaltura\Client\Type\ClientConfiguration $configuration)
	{
		$params = get_class_vars('\Kaltura\Client\Type\ClientConfiguration');
		foreach($params as $param)
		{
			if(is_null($configuration->$param))
			{
				if(isset($this->clientConfiguration[$param]))
				{
					unset($this->clientConfiguration[$param]);
				}
			}
			else
			{
				$this->clientConfiguration[$param] = $configuration->$param;
			}
		}
	}

	public function setRequestConfiguration(\Kaltura\Client\Type\RequestConfiguration $configuration)
	{
		$params = get_class_vars('\Kaltura\Client\Type\RequestConfiguration');
		foreach($params as $param)
		{
			if(is_null($configuration->$param))
			{
				if(isset($this->requestConfiguration[$param]))
				{
					unset($this->requestConfiguration[$param]);
				}
			}
			else
			{
				$this->requestConfiguration[$param] = $configuration->$param;
			}
		}
	}

	/**
	 * Add parameter to array of parameters that is passed by reference
	 *
	 * @param array $params
	 * @param string $paramName
	 * @param string $paramValue
	 */
	public function addParam(array &$params, $paramName, $paramValue)
	{
		if ($paramValue === null)
			return;

		if ($paramValue instanceof NullValue) {
			$params[$paramName . '__null'] = '';
			return;
		}

		if(is_object($paramValue) && $paramValue instanceof ObjectBase)
		{
			$params[$paramName] = array(
				'objectType' => get_class($paramValue)
			);
			
			foreach($paramValue as $prop => $val)
				$this->addParam($params[$paramName], $prop, $val);

			return;
		}

		if(is_bool($paramValue))
		{
			$params[$paramName] = $paramValue ? 'true' : 'false';
			return;
		}

		if(!is_array($paramValue))
		{
			$params[$paramName] = (string)$paramValue;
			return;
		}

		$params[$paramName] = array();
		if ($paramValue)
		{
			foreach($paramValue as $subParamName => $subParamValue)
				$this->addParam($params[$paramName], $subParamName, $subParamValue);
		}
		else
		{
			$params[$paramName]['-'] = '';
		}
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function jsObjectToClientObject($value)
	{
		if(is_array($value))
		{
			foreach($value as &$item)
			{
				$item = $this->jsObjectToClientObject($item);
			}
		}
		
		if(is_object($value))
		{
			if(isset($value->message) && isset($value->code))
			{
				if($this->isMultiRequest)
				{
					if(isset($value->args))
					{
						$value->args = (array) $value->args;
					}
					return (array) $value;
				}
				throw new KalturaException($value->message, $value->code, $value->args);
			}
			
			if(!isset($value->objectType))
			{
				throw new ClientException("Response format not supported - objectType is required for all objects", ClientException::ERROR_FORMAT_NOT_SUPPORTED);
			}
			
			$objectType = $value->objectType;
			$object = new $objectType();
			$attributes = get_object_vars($value);
			foreach($attributes as $attribute => $attributeValue)
			{
				if($attribute === 'objectType')
				{
					continue;
				}
				
				$object->$attribute = $this->jsObjectToClientObject($attributeValue);
			}
			
			$value = $object;
		}
		
		return $value;
	}

	/**
	 * Encodes objects
	 * @param mixed $value
	 * @return string
	 */
	public function jsonEncode($value)
	{
		return json_encode($this->unsetNull($value));
	}

	protected function unsetNull($object)
	{
		if(!is_array($object) && !is_object($object))
			return $object;
		
		if(is_object($object) && $object instanceof MultiRequestSubResult)
			return "$object";
		
		$array = (array) $object;
		foreach($array as $key => $value)
		{
			if(is_null($value))
			{
				unset($array[$key]);
			}
			else
			{
				$array[$key] = $this->unsetNull($value);
			}
		}

		if(is_object($object))
			$array['objectType'] = get_class($object);
			
		return $array;
	}

	/**
	 * Validate that the passed object type is of the expected type
	 *
	 * @param any $resultObject
	 * @param string $objectType
	 */
	public function validateObjectType($resultObject, $objectType)
	{
		$knownNativeTypes = array("boolean", "integer", "double", "string");
		if (is_null($resultObject) ||
			( in_array(gettype($resultObject) ,$knownNativeTypes) &&
			  in_array($objectType, $knownNativeTypes) ) )
		{
			return;// we do not check native simple types
		}
		else if ( is_object($resultObject) )
		{
			if (!($resultObject instanceof $objectType))
			{
				throw new ClientException("Invalid object type - not instance of $objectType", ClientException::ERROR_INVALID_OBJECT_TYPE);
			}
		}
		else if(class_exists($objectType) && is_subclass_of($objectType, 'EnumBase'))
		{
			$enum = new ReflectionClass($objectType);
			$values = array_map('strval', $enum->getConstants());
			if(!in_array($resultObject, $values))
			{
				throw new ClientException("Invalid enum value", ClientException::ERROR_INVALID_ENUM_VALUE);
			}
		}
		else if(gettype($resultObject) !== $objectType)
		{
			throw new ClientException("Invalid object type", ClientException::ERROR_INVALID_OBJECT_TYPE);
		}
	}

	public function startMultiRequest()
	{
		$this->multiRequestReturnType = array();
	}

	public function doMultiRequest()
	{
		$xmlData = $this->doQueue();
		if(is_null($xmlData))
			return null;

		$xml = new \SimpleXMLElement($xmlData);
		$items = $xml->result->children();
		$ret = array();
		$i = 0;
		foreach($items as $item) {
			$error = ParseUtils::checkIfError($item, false);
			if($error)
				$ret[] = $error;
			else if($item->objectType)
				$ret[] = ParseUtils::unmarshalObject($item, $this->multiRequestReturnType[$i]);
			else if($item->item)
				$ret[] = ParseUtils::unmarshalArray($item, $this->multiRequestReturnType[$i]);
			else
				$ret[] = ParseUtils::unmarshalSimpleType($item);
			$i++;
		}

		$this->resetRequest();
		return $ret;
	}

	public function isMultiRequest()
	{
		return !is_null($this->multiRequestReturnType);
	}

	public function getMultiRequestQueueSize()
	{
		return count($this->callsQueue);
	}

	public function getMultiRequestResult()
	{
		return new MultiRequestSubResult($this->getMultiRequestQueueSize() . ':result');
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
	 * Return a list of parameters used to start a new debug session on the destination server api
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
		return NullValue::getInstance();
	}

	public function __get($prop)
	{
		$getter = 'get'.ucfirst($prop).'Service';
		if (method_exists($this, $getter))
			return $this->$getter();
	}
}
