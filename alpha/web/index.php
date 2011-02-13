<?php

require_once(dirname(__FILE__).'/../config/kConf.php');
 
function checkCache()
{
	$start_time = microtime(true);

	$uri = $_SERVER["REQUEST_URI"];
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";

	if (strpos($uri, "/partnerservices2") !== false)
	{
		$params = $_GET + $_POST;
		unset($params['ks']);
		unset($params['kalsig']);
		$params['uri'] = $_SERVER['PATH_INFO'];
		ksort($params);

		$keys = array_keys($params);
		$key = md5(implode("|", $params).implode("|", $keys));

		$cache_filename = "/tmp/cache-$key";

		if (file_exists($cache_filename))
		{
			if (filemtime($cache_filename) + 600 < time())
			{
				@unlink($cache_filename);
				@unlink($cache_filename.".headers");
				@unlink($cache_filename.".log");
			}
            else
            {
            	$content_type = @file_get_contents("/tmp/cache-$key.headers");
				if ($content_type)
					header("Content-Type: $content_type");
					
            	$response = @file_get_contents("/tmp/cache-$key");
                if ($response)
                {
					$processing_time = microtime(true) - $start_time;
					header("X-Kaltura:cached-dispatcher,$key,$processing_time");
					header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
					header("Cache-Control" , "no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
					header("Pragma" , "no-cache" );
					echo $response;
					die;
				}
			}
		}
	}

	if (strpos($uri, "/kwidget") !== false)	
	{
		if (!function_exists('memcache_connect')) return;
						
		$cache = new Memcache;
		$res = @$cache->connect("localhost", "11211");
		if ( $res )
		{
			// check if we cached the patched swf with flashvars
			$uri = $protocol.$uri;
			$cachedResponse = $cache->get("kwidgetswf$uri");
			if ($cachedResponse) // dont use cache if we want to force no caching
			{
				$max_age = 60 * 10;
				header("X-Kaltura:cached-dispatcher");
				header("Content-Type: application/x-shockwave-flash");
				header("Cache-Control: private, max-age=$max_age max-stale=0");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
				header('Last-Modified: Thu, 19 Nov 2000 08:52:00 GMT');
				header("Content-Length: ".strlen($cachedResponse));
				echo $cachedResponse;
				die;
			}
			
			$cachedResponse = $cache->get("kwidget$uri");
			if ($cachedResponse)
			{
				// set our uv cookie
				$uv_cookie = @$_COOKIE['uv'];
				if (strlen($uv_cookie) != 35)
				{
					$uv_cookie = "uv_".md5(uniqid(rand(), true));
				}
				setrawcookie( 'uv', $uv_cookie, time() + 3600 * 24 * 365, '/' );
		
				header("X-Kaltura:cached-dispatcher");
				header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
				header("Cache-Control" , "no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
				header("Pragma" , "no-cache" );

				if (strpos($uri, "nowrapper") !== false)
				{
    			    header("Location:$cachedResponse");
			        die;
				}				
				
				$referer = @$_SERVER['HTTP_REFERER'];
				
				$externalInterfaceDisabled = (
					strstr($referer, "bebo.com") === false &&
					strstr($referer, "myspace.com") === false &&
					strstr($referer, "current.com") === false &&
					strstr($referer, "myyearbook.com") === false &&
					strstr($referer, "facebook.com") === false &&
					strstr($referer, "friendster.com") === false) ? "" : "&externalInterfaceDisabled=1";

				$noncached_params = $externalInterfaceDisabled."&referer=".urlencode($referer);
					
				if (strpos($cachedResponse, "/swfparams/") > 0)
					$cachedResponse = substr($cachedResponse, 0, -4).urlencode($noncached_params).".swf";
				else
					$cachedResponse .= $noncached_params;
					
				header("Location:$cachedResponse");
				die;
			}
		}
		
	}
	else if (strpos($uri, "/thumbnail") !== false)	
	{
		if (!function_exists('memcache_connect')) return;
						
		$cache = new Memcache;
		$res = @$cache->connect("localhost", "11211");
		if ( $res )
		{
			$file_name = $cache->get("thumb$uri");
			if ($file_name && file_exists($file_name))
			{
				$ext = pathinfo ($file_name, PATHINFO_EXTENSION);
				if ($ext == "jpg")
					$content_type ="image/jpeg";
				else
					$content_type ="image/$ext";
		
				$total_length = filesize($file_name);
				$max_age = 8640000;
				
				header("X-Kaltura:cached-dispatcher-thumb");
				header("Cache-Control: public, max-age=$max_age max-stale=0");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
				header('Last-Modified: Thu, 19 Nov 2000 08:52:00 GMT');
				header("Content-Length: $total_length ");
				header("Pragma:");
				header("Content-Type: $content_type");
				
				$name_of_file = pathinfo($file_name, PATHINFO_FILENAME);
				//$local_file_name = str_replace("/web//content/entry/tempthumb/", "/opt/kaltura/cache/content/entry/tempthumb/", $file_name);
				$local_file_name = kConf::get('general_cache_dir').DIRECTORY_SEPARATOR.'content/entry/tempthumb/'.$name_of_file;

				if (!file_exists($local_file_name))
				{
					$dirname = pathinfo($local_file_name, PATHINFO_DIRNAME);
					if (!file_exists($dirname) || !is_dir($dirname))
						mkdir($dirname, 0755, true);
						
					copy($file_name, $local_file_name);
					
					// if copy succeeded use local file
					if (file_exists($local_file_name))
						$file_name = $local_file_name;
				}
				else
					$file_name = $local_file_name;
				
				$chunk_size = 100000;
				$fh = fopen($file_name, "rb");
				if ($fh)
				{
					$pos = 0;
					while ($total_length >= 0)
					{
						$content = fread( $fh , $chunk_size );
						echo $content;
						$total_length -= $chunk_size;
					}
					fclose($fh);
				}
				
				die;
			}
		}
	}
}


checkCache();

require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

KalturaLog::setLogger(sfContext::getInstance()->getLogger());
ActKeyUtils::checkCurrent();
sfContext::getInstance()->getController()->dispatch();
