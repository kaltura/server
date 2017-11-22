<?php
/**
 * @package infra
 * @subpackage Storage
 */

class kFileUtils extends kFile
{

	const ENCRYPT = '_ENCRYPT';

	public static function pollFileExists($file_name)
	{
		$nfs_file_tries = 0;
		while(! file_exists($file_name))
		{
			//			clearstatcache(true,$file_name);
			clearstatcache();
			$nfs_file_tries ++;
			if($nfs_file_tries > 3) // if after 9 seconds file did not appear in NFS - probably not found...
			{
				break;
					
				// when breaking, kFile will try to dump, if file not exist - will die...
			}
			else
			{
				sleep(3);
			}
		}
	}
	
	public static function xSendFileAllowed($file_name)
	{
		$xsendfile_uri = kConf::hasParam('xsendfile_uri') ? kConf::get('xsendfile_uri') : null;
		if ($xsendfile_uri === null || strpos($_SERVER["REQUEST_URI"], $xsendfile_uri) === false)
			return false;
		
		// Note: xsend-file requires explicit listing of paths that are allowed for file dumping,
		//		the parameter xsendfile_paths should be configured exactly the same as in the apache.conf 
		$xsendfile_paths = kConf::hasParam('xsendfile_paths') ? kConf::get('xsendfile_paths') : array();
		foreach($xsendfile_paths as $path)
		{
			if (strpos($file_name, $path) === 0)
			{
				return true;
			}
		}
		return false;
	}

	public static function getDumpFileRenderer($filePath, $mimeType, $maxAge = null, $limitFileSize = 0, $lastModified = null, $key = null, $iv = null)
	{
		self::closeDbConnections();
		
		self::pollFileExists($filePath);
		
		// if by now there is no file - die !
		if(! file_exists($filePath))
			KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
		
		return new kRendererDumpFile($filePath, $mimeType, self::xSendFileAllowed($filePath), $maxAge, $limitFileSize, $lastModified, $key, $iv);
	}
	
	public static function dumpFile($file_name, $mime_type = null, $max_age = null, $limit_file_size = 0, $key = null, $iv = null)
	{
		$renderer = self::getDumpFileRenderer($file_name, $mime_type, $max_age, $limit_file_size, null, $key, $iv);
		
		$renderer->output();
		
		KExternalErrors::dieGracefully();
	}

	public static function isAlreadyInDumpApi()
	{
		return isset($_SERVER["HTTP_X_KALTURA_PROXY"]);
	}
	
	public static function dumpApiRequest($host, $onlyIfAvailable = false)
	{
		if($onlyIfAvailable){
			//validate that the other DC is available before dumping the request
			if(kConf::hasParam('disable_dump_api_request') && kConf::get('disable_dump_api_request')){
				KalturaLog::info('dumpApiRequest is disabled');
				return;
			}			
		}
		if (kCurrentContext::$multiRequest_index > 1)
            KExternalErrors::dieError(KExternalErrors::MULTIREQUEST_PROXY_FAILED);
		self::closeDbConnections();
		
		// prevent loop back of the proxied request by detecting the "X-Kaltura-Proxy header
		if (isset($_SERVER["HTTP_X_KALTURA_PROXY"]))
			KExternalErrors::dieError(KExternalErrors::PROXY_LOOPBACK);
			
		$get_params = $post_params = array();
		
		// pass uploaded files by adding them as post data with curl @ prefix
		// signifying a file. the $_FILES[xxx][tmp_name] points to the location
		// of the uploaded file.
		// we preserve the original file name by passing the extra ;filename=$_FILES[xxx][name]
		foreach($_FILES as $key => $value)
		{
			$post_params[$key] = "@".$value['tmp_name'].";filename=".$value['name'];
			if(!is_uploaded_file($value['tmp_name'])) {
				KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			}
		}
		
		foreach($_POST as $key => $value)
		{
			$post_params[$key] = $value;
		}
		
		$url = $_SERVER['REQUEST_URI'];
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && kConf::hasParam('https_param_salt'))
		{
			$concatStr = strpos($url, "?") !== false ? "&" : "?";
			$url = $url . $concatStr . 'apiProtocol=https_' . kConf::get('https_param_salt');
		}
			
		$httpHeader = array("X-Kaltura-Proxy: dumpApiRequest");
		
		if(isset(infraRequestUtils::$jsonData))
		{
			$post_params['json'] = infraRequestUtils::$jsonData;
			$httpHeader[] = "Content-Type: multipart/form-data";
		}
		
	  	$ipHeader = infraRequestUtils::getSignedIpAddressHeader();
	  	if ($ipHeader){
	  		list($headerName, $headerValue) = $ipHeader;
	  		$httpHeader[] = ($headerName . ": ". $headerValue);
	  	}
	  	
		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $host . $url );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
		// Set callback function for body
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'kFileUtils::read_body');
		// Set callback function for headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'kFileUtils::read_header');
		
		header("X-Kaltura:dumpApiRequest " . kDataCenterMgr::getCurrentDcId());
		// grab URL and pass it to the browser
		$content = curl_exec($ch);
		
		// close curl resource, and free up system resources
		curl_close($ch);
		KExternalErrors::dieGracefully();
	}
	
    public static function dumpUrl($url, $allowRange = true, $passHeaders = false, $additionalHeaders = null)
	{
		KalturaLog::debug("URL [$url], $allowRange [$allowRange], $passHeaders [$passHeaders]");
		self::closeDbConnections();
	
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		// in case of private ips (internal to the datacenters) no need to check the certificate validity.
		// otherwise curling for https://127.0.0.1/ will fail as the certificate is for *.domain.com
		$urlHost = parse_url($url, PHP_URL_HOST);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, infraRequestUtils::isIpPrivate($urlHost) ? 0 : 2);


		// prevent loop back of the proxied request by detecting the "X-Kaltura-Proxy header
		if (isset($_SERVER["HTTP_X_KALTURA_PROXY"]))
			KExternalErrors::dieError(KExternalErrors::PROXY_LOOPBACK);
			
		$sendHeaders = array("X-Kaltura-Proxy: dumpUrl");
		
		$ipHeader = infraRequestUtils::getSignedIpAddressHeader();
		if ($ipHeader){
			list($headerName, $headerValue) = $ipHeader;
			$sendHeaders[] = ($headerName . ": ". $headerValue);
		}
				
		if($passHeaders)
		{
			$sentHeaders = self::getRequestHeaders();
			foreach($sentHeaders as $header => $value)
				$sendHeaders[] = "$header: $value";
		}
		elseif($allowRange && isset($_SERVER['HTTP_RANGE']) && $_SERVER['HTTP_RANGE'])
		{
			// get range parameters from HTTP range requst headers
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			curl_setopt($ch, CURLOPT_RANGE, $range);
		}

		if ($additionalHeaders)
		{
			foreach($additionalHeaders as $header => $value)
                       		$sendHeaders[] = "$header: $value";
		}
		
		// when proxying request to other datacenter we may be already in a proxied request (from one of the internal proxy servers)
		// we need to ensure the original HOST is sent in order to allow restirctions checks

		$host = kConf::get('www_host');
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
			$host =  $_SERVER['HTTP_X_FORWARDED_HOST'];
		else if (isset($_SERVER['HTTP_HOST']))
			$host = $_SERVER['HTTP_HOST'];

		for($i = 0; $i < count($sendHeaders); $i++)
		{
			if (stripos($sendHeaders[$i], "host:") === 0)
			{
				array_splice($sendHeaders, $i, 1);
				break;
			}
		}

		$sendHeaders[] = "Host:$host";

		curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);

		if($_SERVER['REQUEST_METHOD'] == 'HEAD')
		{
			// request was HEAD, proxy only HEAD response
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		}
		else
		{
			// Set callback function for body
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'kFileUtils::read_body');
		}
		// Set callback function for headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'kFileUtils::read_header');
		
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		header("Access-Control-Allow-Origin:*"); // avoid html5 xss issues
		header("X-Kaltura:dumpUrl");
		// grab URL and pass it to the browser
		$content = curl_exec($ch);
		KalturaLog::debug("CURL executed [$content]");
		
		// close curl resource, and free up system resources
		curl_close($ch);
		
		KExternalErrors::dieGracefully();
	}

	static public function addEncryptToFileName($fileName)
	{
		$typeLen = strlen(pathinfo($fileName, PATHINFO_EXTENSION)) + 1;
		$pos = strlen($fileName) - $typeLen;
		return substr($fileName, 0, $pos) . self::ENCRYPT . substr($fileName, $pos);
	}

	static public function isFileEncrypt($fileName)
	{
		$pos = strpos($fileName, self::ENCRYPT);
		$PrefixLen = strlen(pathinfo($fileName, PATHINFO_EXTENSION)) + 1 + strlen(self::ENCRYPT);
		return (($pos+$PrefixLen) == strlen($fileName));
	}


}
