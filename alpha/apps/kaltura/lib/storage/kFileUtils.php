<?php
/**
 * @package infra
 * @subpackage Storage
 */
class kFileUtils extends kFile
{
	public static function dumpFile($file_name, $mime_type = null, $max_age = null, $limit_file_size = 0)
	{
		self::closeDbConnections();
		
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
		
		// if by now there is no file - die !
		if(! file_exists($file_name))
			KExternalErrors::dieGracefully();
		
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$total_length = $limit_file_size ? $limit_file_size : self::fileSize($file_name);
		
		$useXSendFile = false;
		if (!$limit_file_size && // if we limit the file size (e.g. preview small portion of a video) we can't use xsendfile module
			in_array('mod_xsendfile', apache_get_modules()))
		{
			$xsendfile_uri = kConf::hasParam('xsendfile_uri') ? kConf::get('xsendfile_uri') : null;
			if ($xsendfile_uri !== null && strpos($_SERVER["REQUEST_URI"], $xsendfile_uri) !== false)
			{
				$xsendfile_paths = kConf::hasParam('xsendfile_paths') ? kConf::get('xsendfile_paths') : array();
				foreach($xsendfile_paths as $path)
				{
					if (strpos($file_name, $path) === 0)
					{
						header('X-Kaltura-Sendfile:');
						$useXSendFile = true;
						break;
					}
				}
			}
		}

		if ($useXSendFile)
			$range_length = null;
		else
		{
			// get range parameters from HTTP range requst headers
			list($range_from, $range_to, $range_length) = infraRequestUtils::handleRangeRequest($total_length);
		}
		
		if($mime_type)
		{
			infraRequestUtils::sendCdnHeaders($file_name, $range_length, $max_age, $mime_type);
		}
		else
			infraRequestUtils::sendCdnHeaders($ext, $range_length, $max_age);

		// return "Accept-Ranges: bytes" header. Firefox looks for it when playing ogg video files
		// upon detecting this header it cancels its original request and starts sending byte range requests
		header("Accept-Ranges: bytes");
		header("Access-Control-Allow-Origin:*");		

		if ($useXSendFile)
		{
			if (isset($GLOBALS["start"]))
				header("X-Kaltura:dumpFile:".(microtime(true) - $GLOBALS["start"]));
			header("X-Sendfile: $file_name");
			KExternalErrors::dieGracefully();
		}

		$chunk_size = 100000;
		$fh = fopen($file_name, "rb");
		if($fh)
		{
			$pos = 0;
			fseek($fh, $range_from);
			while($range_length > 0)
			{
				$content = fread($fh, min($chunk_size, $range_length));
				echo $content;
				$range_length -= $chunk_size;
			}
			fclose($fh);
		}
		
		KExternalErrors::dieGracefully();
	}

	public static function dumpApiRequest($host)
	{
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
		}
		
		foreach($_POST as $key => $value)
		{
			$post_params[$key] = $value;
		}
		
		$url = $_SERVER['REQUEST_URI'];
		
		$ch = curl_init();
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $host . $url );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Kaltura-Proxy: dumpApiRequest"));
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
	
    public static function dumpUrl($url, $allowRange = true, $passHeaders = false)
	{
		KalturaLog::debug("URL [$url], $allowRange [$allowRange], $passHeaders [$passHeaders]");
		self::closeDbConnections();
		
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");

		// prevent loop back of the proxied request by detecting the "X-Kaltura-Proxy header
		if (isset($_SERVER["HTTP_X_KALTURA_PROXY"]))
			KExternalErrors::dieError(KExternalErrors::PROXY_LOOPBACK);
			
		$sendHeaders = array("X-Kaltura-Proxy: dumpUrl");
		
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
		
		// when proxying request to other datacenter we may be already in a proxied request (from one of the internal proxy servers)
		// we need to ensure the original HOST is sent in order to allow restirctions checks

		$host = isset($_SERVER["HTTP_X_FORWARDED_HOST"]) ? $_SERVER["HTTP_X_FORWARDED_HOST"] : $_SERVER["HTTP_HOST"];

		for($i = 0; $i < count($sendHeaders); $i++)
		{
			if (strpos($sendHeaders[$i], "HOST:") === 0)
			{
				array_splice($sendHeaders, $i, 1);
				break;
			}
		}

		$sendHeaders[] = "HOST:$host";

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
}
