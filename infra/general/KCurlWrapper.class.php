<?php

/**
 *  @package infra
 *  @subpackage general
 */

class KCurlHeaderResponse
{
	const HTTP_STATUS_CONTINUE = 100; //The request can be continued.
	const HTTP_STATUS_SWITCH_PROTOCOLS = 101; //The server has switched protocols in an upgrade header.
	const HTTP_STATUS_OK = 200; //The request completed successfully.
	const HTTP_STATUS_CREATED = 201; //The request has been fulfilled and resulted in the creation of a new resource.
	const HTTP_STATUS_ACCEPTED = 202; //The request has been accepted for processing, but the processing has not been completed.
	const HTTP_STATUS_PARTIAL = 203; //The returned meta information in the entity-header is not the definitive set available from the originating server.
	const HTTP_STATUS_NO_CONTENT = 204; //The server has fulfilled the request, but there is no new information to send back.
	const HTTP_STATUS_RESET_CONTENT = 205; //The request has been completed, and the client program should reset the document view that caused the request to be sent to allow the user to easily initiate another input action.
	const HTTP_STATUS_PARTIAL_CONTENT = 206; //The server has fulfilled the partial GET request for the resource.
	const HTTP_STATUS_WEBDAV_MULTI_STATUS = 207; //During a World Wide Web Distributed Authoring and Versioning (WebDAV) operation, this indicates multiple status codes for a single response. The response body contains Extensible Markup Language (XML) that describes the status codes. For more information, see HTTP Extensions for Distributed Authoring.
	const HTTP_STATUS_AMBIGUOUS = 300; //The requested resource is available at one or more locations.
	const HTTP_STATUS_MOVED = 301; //The requested resource has been assigned to a new permanent Uniform Resource Identifier (URI), and any future references to this resource should be done using one of the returned URIs.
	const HTTP_STATUS_REDIRECT = 302; //The requested resource resides temporarily under a different URI.
	const HTTP_STATUS_REDIRECT_METHOD = 303; //The response to the request can be found under a different URI and should be retrieved using a GET HTTP verb on that resource.
	const HTTP_STATUS_NOT_MODIFIED = 304; //The requested resource has not been modified.
	const HTTP_STATUS_USE_PROXY = 305; //The requested resource must be accessed through the proxy given by the location field.
	const HTTP_STATUS_REDIRECT_KEEP_VERB = 307; //The redirected request keeps the same HTTP verb. HTTP/1.1 behavior.
	const HTTP_STATUS_BAD_REQUEST = 400; //The request could not be processed by the server due to invalid syntax.
	const HTTP_STATUS_DENIED = 401; //The requested resource requires user authentication.
	const HTTP_STATUS_PAYMENT_REQ = 402; //Not implemented in the HTTP protocol.
	const HTTP_STATUS_FORBIDDEN = 403; //The server understood the request, but cannot fulfill it.
	const HTTP_STATUS_NOT_FOUND = 404; //The server has not found anything that matches the requested URI.
	const HTTP_STATUS_BAD_METHOD = 405; //The HTTP verb used is not allowed.
	const HTTP_STATUS_NONE_ACCEPTABLE = 406; //No responses acceptable to the client were found.
	const HTTP_STATUS_PROXY_AUTH_REQ = 407; //Proxy authentication required.
	const HTTP_STATUS_REQUEST_TIMEOUT = 408; //The server timed out waiting for the request.
	const HTTP_STATUS_CONFLICT = 409; //The request could not be completed due to a conflict with the current state of the resource. The user should resubmit with more information.
	const HTTP_STATUS_GONE = 410; //The requested resource is no longer available at the server, and no forwarding address is known.
	const HTTP_STATUS_LENGTH_REQUIRED = 411; //The server cannot accept the request without a defined content length.
	const HTTP_STATUS_PRECOND_FAILED = 412; //The precondition given in one or more of the request header fields evaluated to false when it was tested on the server.
	const HTTP_STATUS_REQUEST_TOO_LARGE = 413; //The server cannot process the request because the request entity is larger than the server is able to process.
	const HTTP_STATUS_URI_TOO_LONG = 414; //The server cannot service the request because the request URI is longer than the server can interpret.
	const HTTP_STATUS_UNSUPPORTED_MEDIA = 415; //The server cannot service the request because the entity of the request is in a format not supported by the requested resource for the requested method.
	const HTTP_STATUS_RETRY_WITH = 449; //The request should be retried after doing the appropriate action.
	const HTTP_STATUS_SERVER_ERROR = 500; //The server encountered an unexpected condition that prevented it from fulfilling the request.
	const HTTP_STATUS_NOT_SUPPORTED = 501; //The server does not support the functionality required to fulfill the request.
	const HTTP_STATUS_BAD_GATEWAY = 502; //The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.
	const HTTP_STATUS_SERVICE_UNAVAIL = 503; //The service is temporarily overloaded.
	const HTTP_STATUS_GATEWAY_TIMEOUT = 504; //The request was timed out waiting for a gateway.
	const HTTP_STATUS_VERSION_NOT_SUP = 505; //The server does not support the HTTP protocol version that was used in the request message.

	/**
	 * @var int
	 */
	public $code;

	/**
	 * @var string
	 */
	public $codeName;

	/**
	 * @var array
	 */
	public $headers = array();

	/**
	 * @return boolean
	 */
	public function isGoodCode()
	{
		$goodCodes = array(
			self::HTTP_STATUS_OK => true,
			self::HTTP_STATUS_PARTIAL_CONTENT => true,
			self::HTTP_STATUS_REDIRECT => true,
			self::HTTP_STATUS_MOVED => true,
		);

		return isset($goodCodes[$this->code]);
	}

	public static function isError($httpCode)
	{
		$restype = floor($httpCode / 100);
		if ($restype == 4 || $restype == 5)
			return true;
		return false;
	}

	public function storeCookie($value)
	{
		$cookieKey=trim($this->getCookieKey($value));
		$this->headers['set-cookie'][$cookieKey]=trim($value);
	}

	public function getCookieValue($cookieInfo,$cookieKey)
	{
		//search cookie value in curlInfo
		if (!isset($cookieInfo[$cookieKey]))
		{
			throw new Exception("Cookie key not found-".$cookieKey);
		}

		$cookie = $cookieInfo[$cookieKey];
		$cookieVars = explode(';',$cookie);
		foreach ($cookieVars as $cookieVar)
		{
			$keyVal = explode('=',$cookieVar);
			if($keyVal[0]==$cookieKey)
			{
				return $keyVal[1];
			}
		}

		return null;
	}

	private function getCookieKey($setCookieValue)
	{
		//list cookie vars
		$cookieItems = explode(';',$setCookieValue);

		//Get the cookie key
		$cookieKey = explode ('=',$cookieItems[0]);

		return $cookieKey[0];
	}
}

/**
 * A small wrapper for the curl command - will make sure the destructor will close the relevant handles.
 *
 * @package Scheduler
 */
class KCurlWrapper
{
	const HTTP_PROTOCOL_HTTP = 1;
	const HTTP_PROTOCOL_FTP = 2;
	const COULD_NOT_CONNECT_TO_HOST_ERROR = "couldn't connect to host";
	
	//curl idle const configuration
	const LOW_SPEED_BYTE_LIMIT = 1; //1 byte per sec
	const LOW_SPEED_TIME_LIMIT = 595; //595 sec + 5 sec until it is detected total is 600 sec = 10 min

	const HTTP_USER_AGENT = "\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6\"";
	
	const ERROR_CODE_FAILED_TO_OPEN_FILE_FOR_WRITING = 37;

	/**
	 * @var resource
	 */
	public $ch;

	public $protocol;

	private static $headers;
	private static $lastHeader;

	/**
	 * @var int
	 */
	public $errorNumber;

	/**
	 * @var false|string
	 */
	public $error;

	/**
	 * @var int
	 */
	public $httpCode = 0;

	private static function read_header($ch, $string)
	{
		return strlen($string);
	}

	private static function read_header_validate_redirect($ch, $string)
	{
		$prefix = 'location:';
        if (strtolower(substr($string, 0, strlen($prefix))) != $prefix)
		{
			return strlen($string);
		}

		$url = trim(substr(trim($string), strlen($prefix)));
		KalturaLog::debug("Validating redirect url [$url]");

		$parts = parse_url($url);
		if (!isset($parts['scheme']) || !isset($parts['host']))
		{
			KalturaLog::log("Failed to parse redirect url [$url]");
			return 0;
		}

		if (self::isInternalHost($parts['host']) && !self::isWhiteListedInternalUrl($url))
		{
			KalturaLog::log("Redirect url [$url] is internal and not whiteListed");
			return 0;
		}

		if (isset($parts['user']) && strpos($parts['user'], '@') !== false)
		{
			KalturaLog::log("Redirect url [$url] user contains @");
			return 0;
		}

		if (isset($parts['pass']) && strpos($parts['pass'], '@') !== false)
		{
			KalturaLog::log("Redirect url [$url] pass contains @");
			return 0;
		}

		return strlen($string);
	}

	private static function read_header_store($ch, $string)
	{
		self::$headers .= $string;
		if ($string == "\r\n")
		{
			$curlInfo = curl_getinfo($ch);
			$httpResponseCode = $curlInfo['http_code'];
			if(!in_array($httpResponseCode, array(KCurlHeaderResponse::HTTP_STATUS_REDIRECT, KCurlHeaderResponse::HTTP_STATUS_MOVED))) // mark when we get to the last header so we can abort the cur
				self::$lastHeader = true;
		}
		
		return strlen($string);
	}

	private static function read_header_store_validate_redirect($ch, $string)
	{
		self::read_header_store($ch, $string);
		return self::read_header_validate_redirect($ch, $string);
	}


	private static function read_body($ch, $string) {
		if (self::$lastHeader) // if we read the last header abort the curl
			return 0;

		$length = strlen ( $string );
		return $length;
	}
	
	private static function read_body_do_nothing($ch, $string) {
		$length = strlen ( $string );
		return $length;
	}

	/**
	 * @param string $url
	 */
	public function __construct($params = null) {
		// this is the default - will change only in very specific conditions (bellow)
		$this->protocol = self::HTTP_PROTOCOL_HTTP;
		$this->ch = curl_init();

		// set appropriate options - these can be overriden with the setOpt function if desired
		curl_setopt($this->ch, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		
		//Add curl idle check options in this chase we check if for the past 10 min the avg transfer rate is over 1 byte per sec
		//These configurations can be overriden with the setOpt function if desired
		$curlLowSpeedByteLimit = self::LOW_SPEED_BYTE_LIMIT;
		if($params && isset($params->curlLowSpeedByteLimit) && $params->curlLowSpeedByteLimit)
			$curlLowSpeedByteLimit = $params->curlLowSpeedByteLimit;
		
		$curlLowSpeedTimeLimit = self::LOW_SPEED_TIME_LIMIT;
		if($params && isset($params->curlLowSpeedTimeLimit) && $params->curlLowSpeedTimeLimit)
			$curlLowSpeedTimeLimit = $params->curlLowSpeedTimeLimit;
			
		curl_setopt($this->ch, CURLOPT_LOW_SPEED_LIMIT, $curlLowSpeedByteLimit);
		curl_setopt($this->ch, CURLOPT_LOW_SPEED_TIME, $curlLowSpeedTimeLimit);

		curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
		curl_setopt($this->ch, CURLOPT_FORBID_REUSE, true);
		
		if($params && isset($params->curlTimeout) && $params->curlTimeout)
			$this->setTimeout($params->curlTimeout);

		if($params && isset($params->curlDnsCacheTimeout) && $params->curlDnsCacheTimeout)
			curl_setopt($this->ch, CURLOPT_DNS_CACHE_TIMEOUT, $params->curlDnsCacheTimeout);
		
		if($params && isset($params->curlVerbose) && $params->curlVerbose)
			curl_setopt($this->ch, CURLOPT_VERBOSE, true);

		if(!$params || !isset($params->curlVerifySSL) || !$params->curlVerifySSL)
		{
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, self::getSslVerifyHostValue());
		}
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public static function encodeUrl($url)
	{
		return str_replace(array(' ', '[', ']'), array('%20', '%5B', '%5D'), $url);
	}

	/**
	 * This Function can work in two ways if destFilePath is provided it will copy the url to the dest file else will return the file as string as an output
	 * @param string $url - URL to get data from
	 * @param $destFilePath - Optional URL to copy data to
	 * @param int $maxFileSize - Optional max file size allowed for the retrieval action
	 * @return bool|string
	 * @throws Exception | file as string | if $destFilePath provide - true or false
	 */
	
	public static function getDataFromFile($url, $destFilePath = null, $maxFileSize = null, $allowInternalUrl = false)
	{
		if(!is_null($maxFileSize))
		{
			$curlWrapper = new KCurlWrapper();
			$curlHeaderResponse = $curlWrapper->getHeader($url, true, $allowInternalUrl);
			$curlWrapper->close();
			
			if(!$curlHeaderResponse || $curlWrapper->getError())
				throw new Exception("Failed to retrive Curl header response from file path [$url] with Error " . $curlWrapper->getError());
				
			if(!$curlHeaderResponse->isGoodCode())
				throw new Exception("Non Valid Error: $curlHeaderResponse->code" . " " . $curlHeaderResponse->codeName);
			
			if(isset($curlHeaderResponse->headers['content-length']))
			{
				$fileSize = $curlHeaderResponse->headers['content-length'];
				if($fileSize > $maxFileSize)
					throw new Exception("File size [$fileSize] Excedded Max Siae Allowed [$maxFileSize]");
					
				KalturaLog::info("File size [$fileSize] validated");
			}
			else 
			{
				KalturaLog::info("File size validation skipped");
			}
		}
		
		$curlWrapper = new KCurlWrapper();
		$res = $curlWrapper->exec($url, $destFilePath, null, $allowInternalUrl);

		$httpCode = $curlWrapper->getHttpCode();
		if (KCurlHeaderResponse::isError($httpCode))
		{
			KalturaLog::info("curl request [$url] return with http-code of [$httpCode]");
			if ($destFilePath && file_exists($destFilePath))
				unlink($destFilePath);
			$res = false;
		}


		$curlWrapper->close();
		
		return $res;
	}


	/**
	 * @return false|string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return false|string
	 */
	public function getErrorMsg()
	{
		$err = curl_error($this->ch);
		if(!strlen($err))
			return false;

		return $err;
	}

	/**
	 * @param $opt
	 * @return string
	 */
	public function getHttpCode()
	{
		return $this->httpCode;
	}

	/**
	 * @param $opt
	 * @return string|array
	 */
	public function getInfo($opt)
	{
		if (!$opt)
			return curl_getinfo($this->ch);
		return curl_getinfo($this->ch, $opt);
	}

	/**
	 * @return number
	 */
	public function getErrorNumber()
	{
		return $this->errorNumber;
	}

	/**
	 * @param int $opt
	 * @param mixed $val
	 * @return boolean
	 */
	public function setOpt($opt, $val)
	{
		return curl_setopt($this->ch, $opt, $val);
	}

	/**
	 * @param int $seconds
	 * @return boolean
	 */
	public function setTimeout($seconds)
	{
		return $this->setOpt(CURLOPT_TIMEOUT, $seconds);
	}

	/**
	 * @param int $offset
	 * @return boolean
	 */
	public function setResumeOffset($offset)
	{
		return $this->setOpt(CURLOPT_RESUME_FROM, $offset);
	}

	/**
	 * @return false|KCurlHeaderResponse
	 */
	public function getHeader($sourceUrl, $noBody = false, $allowInternalUrl = false)
	{
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, 'KCurlWrapper::read_body');

		if (!self::setSourceUrl($this->ch, $sourceUrl, $this->protocol, $allowInternalUrl, true))
		{
			$this->setInternalUrlErrorResults($sourceUrl);
			return false;
		}

		if($this->protocol == self::HTTP_PROTOCOL_FTP)
			$noBody = true;

		if($noBody)
		{
			curl_setopt($this->ch, CURLOPT_NOBODY, true);
		}
		else
		{
			curl_setopt($this->ch, CURLOPT_RANGE, '0-0');
		}

		self::$headers = "";
		self::$lastHeader = false;

		$this->execCurl();

		//Added to support multiple curl executions using the same curl. Wince this is the same curl re-used we need to reset the range option before continuing forward
		if(!$noBody)
			curl_setopt($this->ch, CURLOPT_RANGE, '0-');

		if(!self::$headers)
		   return false;

		self::$headers = explode("\r\n", self::$headers);

		$curlHeaderResponse = new KCurlHeaderResponse();

		if ( $this->protocol == self::HTTP_PROTOCOL_HTTP )
		{
			$header = reset(self::$headers);

			// this line is true if the protocol is HTTP (or HTTPS);
			if(preg_match('/HTTP\/?[\d.]{0,3}/', $header))
			{
				$matches = explode(" ", $header, 3);
				if(isset($matches[1]) && is_numeric($matches[1]))
				{
					$curlHeaderResponse->code = $matches[1];
					if(isset($matches[2]) && !empty($matches[2]))
						$curlHeaderResponse->codeName = $matches[2];
				}
			}

			foreach ( self::$headers as $header )
			{
				if(!strstr($header, ':'))
				{
					if(preg_match('/HTTP\/?[\d.]{0,3} ([\d]{3}) (.+)/', $header, $matches))
					{
						$curlHeaderResponse->code = $matches[1];
						$curlHeaderResponse->codeName = $matches[2];
					}
					continue;
				}

				list($name, $value) = explode(':', $header, 2);
				if (trim(strtolower($name))=='set-cookie')
				{
					$curlHeaderResponse->storeCookie($value);
				}
				else
				{
					$curlHeaderResponse->headers[trim(strtolower($name))] = (trim($value));
				}

			}

			if(!$noBody)
			{
				$matches = null;
				if(isset($curlHeaderResponse->headers['content-range']) && preg_match('/0-0\/([\d]+)$/', $curlHeaderResponse->headers['content-range'], $matches))
				{
					$curlHeaderResponse->headers['content-length'] = $matches[1];
				}
				else
				{
					return $this->getHeader($sourceUrl, true, $allowInternalUrl);
				}
			}
		}
		else
		{
		// 	for now - assume FTP
			foreach ( self::$headers as $header )
			{
				$headerParts = explode(':', $header, 2);
				if(count($headerParts) < 2)
					continue;

				list($name, $value) = $headerParts;
				if (trim(strtolower($name))=='set-cookie')
				{
					$curlHeaderResponse->storeCookie($value);
				}
				else
				{
					$curlHeaderResponse->headers[trim(strtolower($name))] = (trim($value));
				}
			}

			// if this is a good ftp url - there will be a content-length header
			$length = @$curlHeaderResponse->headers["content-length"];
			if ( $length > 0 )
			{
				// this is equivalent to a good HTTP request
				$curlHeaderResponse->code = KCurlHeaderResponse::HTTP_STATUS_OK;
				$curlHeaderResponse->codeName = "OK";
			}
			else
			{
				if ( isset ( $curlHeaderResponse->headers["curl"] ) )
				{
					// example: curl: (10) the username and/or the password are incorrect
					// in this case set the error code to unknown error and use the whole string as the description
					$curlHeaderResponse->code = -1; // unknown error
					$curlHeaderResponse->codeName = "curl: " . $curlHeaderResponse->headers["curl"] ;
				}
				else
				{
					// example: curl: (10) the username and/or the password are incorrect
					// in this case set the error code to unknown error and use the whole string as the description
					$curlHeaderResponse->code = -1; // unknown error
					$curlHeaderResponse->codeName = "Unknown FTP error" ;
				}
			}
		}

		curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, 'KCurlWrapper::read_body_do_nothing');
		
		return $curlHeaderResponse;
	}

	/**
	 * @param $sourceUrl
	 * @param null $destFile
	 * @param null $progressCallBack
	 * @return mixed
	 * @throws Exception
	 */
	public function exec($sourceUrl, $destFile = null,$progressCallBack = null, $allowInternalUrl = false)
	{
		if (!self::setSourceUrl($this->ch, $sourceUrl, $this->protocol, $allowInternalUrl))
		{
			$this->setInternalUrlErrorResults($sourceUrl);
			return false;
		}

		$returnTransfer = is_null($destFile);
		$destFd = null;
		if (!is_null($destFile))
		{
			$destFd = fopen($destFile, "ab");
			if($destFd === false)
			{
				KalturaLog::debug("Exec Curl - Failed opening file [$destFile] for writing");
				$this->setFailedOpeningFileErrorResults($destFile);
				return false;
			}
		}

		curl_setopt($this->ch, CURLOPT_HEADER, false);
		curl_setopt($this->ch, CURLOPT_NOBODY, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
		if (!is_null($destFd))
			curl_setopt($this->ch, CURLOPT_FILE, $destFd);
		if($progressCallBack)
		{
			curl_setopt($this->ch, CURLOPT_NOPROGRESS, false);
			curl_setopt($this->ch, CURLOPT_PROGRESSFUNCTION, $progressCallBack);
		}
		$ret = $this->execCurl();

		if (!is_null($destFd)) {
			fclose($destFd);
		}

		return $ret;
	}


	/**
	 * @param $sourceUrl
	 * @param $allowInternalUrls
	 * @return mixed
	 */
	public function doExec($sourceUrl, $allowInternalUrl = false)
	{
		if (!self::setSourceUrl($this->ch, $sourceUrl, $this->protocol, $allowInternalUrl))
		{
			$this->setInternalUrlErrorResults($sourceUrl);
			return false;
		}

		$res = $this->execCurl();

		return $res;
	}

	private function setInternalUrlErrorResults($url)
	{
		$this->errorNumber = -1;
		$this->error = "Internal not allowed url [$url] -  curl will not be invoked";
	}
	
	protected function setFailedOpeningFileErrorResults($filePath)
	{
		$this->errorNumber = self::ERROR_CODE_FAILED_TO_OPEN_FILE_FOR_WRITING;
		$this->error = "Failed opening file [$filePath]";
	}

	private function execCurl()
	{
		$res = curl_exec($this->ch);
		$this->httpCode = $this->getInfo(CURLINFO_HTTP_CODE);
		$this->errorNumber = curl_errno($this->ch);
		$this->error = $this->getErrorMsg();
		return $res;
	}

	/**
	 * @param $opts
	 */
	public function setOpts($opts)
	{
		foreach ($opts as $key => $value)
		{
			$this->setOpt($key, $value);
		}
	}

	private static function isInternalHost($host)
	{
		if (!$host)
			return true;
		if (filter_var($host, FILTER_VALIDATE_IP)) // do we have an ip and not a hostname
			return self::IsIpPrivateOrReserved($host);

		$res = gethostbyname($host);
		if ($res == $host) // in case of local machine name
			return true;
		return self::IsIpPrivateOrReserved($res);
	}

	private static function IsIpPrivateOrReserved($host)
	{
		return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE); // checks if host is NOT in a private or reserved range
	}

	private static function isWhiteListedInternalUrl($url)
	{
		if(!kConf::hasMap('security'))
			return true;

		$whiteListedInternalPatterns = kConf::get('internal_url_whitelist', 'security', array());
		foreach ($whiteListedInternalPatterns as $pattern)
		{
			if (preg_match($pattern, $url))
				return true;
		}
		return false;
	}

	public function getSourceUrlProtocol($sourceUrl)
	{
		$protocol = null;
		$sourceUrl = trim($sourceUrl);
		try
		{
			$url_parts = parse_url( $sourceUrl );
			if ( isset ( $url_parts["scheme"] ) )
			{
				if (in_array ($url_parts["scheme"], array ('http', 'https')))
				{
					$protocol = self::HTTP_PROTOCOL_HTTP;
				}
				elseif ( $url_parts["scheme"] == "ftp" || $url_parts["scheme"] == "ftps" )
				{
					$protocol = self::HTTP_PROTOCOL_FTP;
				}
			}
		}
		catch ( Exception $exception )
		{
			throw new Exception($exception->getMessage());
		}
		return $protocol;
	}

	public static function setSourceUrl($ch, $sourceUrl, &$protocol, $allowInternalUrl = false, $readHeader = false)
	{
		$sourceUrl = trim($sourceUrl);
		if (strpos($sourceUrl, '://') === false && substr($sourceUrl, 0, 1) != '/')
		{
			$sourceUrl = 'http://' . $sourceUrl;
		}

		$parts = parse_url($sourceUrl);
		if (!isset($parts['scheme']) || !isset($parts['host']))
		{
			KalturaLog::log("Failed to parse url [$sourceUrl]");
			return false;
		}

		if (!$allowInternalUrl && self::isInternalHost($parts['host']) && !self::isWhiteListedInternalUrl($sourceUrl))
		{
			KalturaLog::log("Url [$sourceUrl] is internal and not whiteListed");
			return false;
		}

		if (in_array($parts['scheme'], array('ftp', 'ftps')))
		{
			$protocol = self::HTTP_PROTOCOL_FTP;
		}
		else
		{
			$protocol = self::HTTP_PROTOCOL_HTTP;
		}

		$userPwd = '';
		if (isset($parts['user']) && isset($parts['pass']))
		{
			if (in_array($parts['scheme'], array('http', 'https')))
			{
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			}
			
			$userPwd = $parts['user'] . ':' . $parts['pass'];
			curl_setopt($ch, CURLOPT_USERPWD, $userPwd);
		}

		$url = $parts['scheme'] . '://' . $parts['host'];
		if (isset($parts['port']))
		{
			$url .= ':' . $parts['port'];
		}

		if (isset($parts['path']))
		{
			$url .= $parts['path'];
		}

		if (isset($parts['query']))
		{
			$url .= '?' . $parts['query'];
		}

		$url = self::encodeUrl($url);

		if ($sourceUrl != $url)
		{
			KalturaLog::info("Input url [$sourceUrl] final url [$url] userpwd [$userPwd]");
		}
		else
		{
			KalturaLog::info("Input url [$url]");
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$headerFunction = 'KCurlWrapper::read_header';
		if ($readHeader)
		{
			$headerFunction .= '_store';
		}
		if (!$allowInternalUrl)
		{
			$headerFunction .= '_validate_redirect';
		}

		curl_setopt($ch, CURLOPT_HEADERFUNCTION, $headerFunction);

		return true;
	}

	public function close()
	{
		curl_close($this->ch);
	}

	// will destroy all the relevant handles
	public function __destruct()
	{
	}

	public static function getContent($url, $headers = null, $allowInternalUrl = false)
	{
		$ch = curl_init();

		// set URL and other appropriate options
		if (!self::setSourceUrl($ch, $url, $protocol, $allowInternalUrl))
		{
			return false;
		}

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		if ($headers)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$content = curl_exec($ch);
		curl_close($ch);
		
		return $content;
	}

	public static function getSslVerifyHostValue()
	{
		$curl_version = curl_version();
		if($curl_version['version_number'] >= 0x071c01 ) // check if curl version is 7.28.1 or higher
		{
			return 2;
		}

		return 1;
	}
}

