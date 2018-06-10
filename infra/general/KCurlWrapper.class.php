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

	/**
	 * @var resource
	 */
	public $ch;

	public $protocol;

	private static $headers;
	private static $lastHeader;

	private static function read_header($ch, $string) {
		self::$headers .= $string;
		if ($string == "\r\n")
		{
			$curlInfo = curl_getinfo($ch);
			$httpResponseCode = $curlInfo['http_code'];
			if(!in_array($httpResponseCode, array(KCurlHeaderResponse::HTTP_STATUS_REDIRECT, KCurlHeaderResponse::HTTP_STATUS_MOVED))) // mark when we get to the last header so we can abort the cur
				self::$lastHeader = true;
		}
		
		$length = strlen ( $string );
		return $length;
	}

	private static function read_body($ch, $string) {
		if (self::$lastHeader) // if we read the last header abort the curl
			return 0;

		$length = strlen ( $string );
		return $length;
	}
	
	private static function read_header_do_nothing($ch, $string) {
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
	 * @throws Exception | file as string | if $destFilePath provide - true or false
	 */
	
	public static function getDataFromFile($url, $destFilePath = null, $maxFileSize = null)
	{
		if(!is_null($maxFileSize))
		{
			$curlWrapper = new KCurlWrapper();
			$curlHeaderResponse = $curlWrapper->getHeader($url, true);
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
		$res = $curlWrapper->exec($url, $destFilePath);

		$httpCode = curl_getinfo($curlWrapper->ch, CURLINFO_HTTP_CODE);
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
		$err = curl_error($this->ch);
		if(!strlen($err))
			return false;

		return $err;
	}
	
	/**
	 * @return string
	 */
	public function getInfo($opt)
	{
		return curl_getinfo($this->ch, $opt);
	}

	/**
	 * @return number
	 */
	public function getErrorNumber()
	{
		return curl_errno($this->ch);
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
	public function getHeader($sourceUrl, $noBody = false)
	{
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, 'KCurlWrapper::read_header');
		curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, 'KCurlWrapper::read_body');
		
		$this->setSourceUrlAndprotocol($sourceUrl);

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
		curl_exec($this->ch);

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
			$matches = null;
			if(preg_match('/HTTP\/?[\d.]{0,3} ([\d]{3}) ([^\n\r]+)/', $header, $matches))
			{
				$curlHeaderResponse->code = $matches[1];
				$curlHeaderResponse->codeName = $matches[2];
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
					return $this->getHeader($sourceUrl, true);
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

		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, 'KCurlWrapper::read_header_do_nothing');
		curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, 'KCurlWrapper::read_body_do_nothing');
		
		return $curlHeaderResponse;
	}

	/**
	 * @param string $sourceUrl
	 * @param string $destFile
	 * @param function $progressCallBack
	 * @return boolean
	 */
	public function exec($sourceUrl, $destFile = null,$progressCallBack = null)
	{
		if ($this->isInternalUrl($sourceUrl))
			KalturaLog::debug("Exec Curl - Found Internal url: " . $sourceUrl);

		$this->setSourceUrlAndprotocol($sourceUrl);
		
		$returnTransfer = is_null($destFile);
		$destFd = null;
		if (!is_null($destFile))
			$destFd = fopen($destFile, "ab");

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
		$ret = curl_exec($this->ch);

		if (!is_null($destFd)) {
			fclose($destFd);
		}

		return $ret;
	}

	private function isInternalUrl($url = null)
	{
		if ((bool)ip2long($url))
			return kIpAddressUtils::isInternalIp($url);
		else
		{
			$res = gethostbyname($url);
			if ($res == $url)
				return false;
			return kIpAddressUtils::isInternalIp($res);
		}
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


	
	public function setSourceUrlAndprotocol($sourceUrl)
	{
		$sourceUrl = trim($sourceUrl);
		try
		{
			$url_parts = parse_url( $sourceUrl );
			if ( isset ( $url_parts["scheme"] ) )
			{
				if ( $url_parts["scheme"] == "ftp" || $url_parts["scheme"] == "ftps" )
					$this->protocol = self::HTTP_PROTOCOL_FTP;
					
				if ( in_array ($url_parts["scheme"], array ('http', 'https')) && isset ($url_parts['user']) )
				{
					curl_setopt ($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				}
			}
			
		}
		catch ( Exception $exception )
		{
			throw new Exception($exception->getMessage());
		}
		KalturaLog::info("Setting source URL to [$sourceUrl]");
		
		$sourceUrl = self::encodeUrl($sourceUrl);
		curl_setopt($this->ch, CURLOPT_URL, $sourceUrl);
	}

	public function close()
	{
		curl_close($this->ch);
	}

	// will destroy all the relevant handles
	public function __destruct()
	{
	}

	public static function getContent($url, $headers = null)
	{
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
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

