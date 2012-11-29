<?php

$start = microtime(true);
require_once(dirname(__FILE__).'/../../server_infra/kConf.php');
 
function checkCache()
{
	$baseDir = "/tmp/cache_v2";

	$start_time = microtime(true);

	$uri = $_SERVER["REQUEST_URI"];
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";

	if (function_exists('apc_fetch'))
	{
		$url = apc_fetch("redirect-".$protocol.$_SERVER["REQUEST_URI"]);
		if ($url)
		{
			$max_age = 60;
			header("Cache-Control: private, max-age=$max_age");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');

			header("X-Kaltura:cached-dispatcher-redirect");
			header("Location:$url");
			die;
		}
	}

	if (strpos($uri, "/playManifest") !== false)
	{
		require_once(dirname(__FILE__)."/../apps/kaltura/lib/cache/kPlayManifestCacher.php");
		$cache = kPlayManifestCacher::getInstance();
		$cache->checkOrStart();
	}
	
	if(strpos($uri, "/partnerservices2") !== false)
	{
		$params = $_GET + $_POST;
		unset($params['ks']);
		unset($params['kalsig']);
		$params['uri'] = $_SERVER['PATH_INFO'];
		ksort($params);

		$keys = array_keys($params);
		$key = md5(implode("|", $params).implode("|", $keys));

		$cache_filename = "$baseDir/cache-$key";

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
            	$content_type = @file_get_contents("$baseDir/cache-$key.headers");
				if ($content_type)
					header("Content-Type: $content_type");
					
            	$response = @file_get_contents("$baseDir/cache-$key");
                if ($response)
                {
                	header("Access-Control-Allow-Origin:*"); // avoid html5 xss issues
					if (strpos($uri, "/partnerservices2/executeplaylist") !== false) // for now cache only playlist on cdn
					{
						$max_age = 60;
						header("Cache-Control: private, max-age=$max_age, max-stale=0");
						header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT');
						header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . 'GMT');
					}
					else
					{
						header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
						header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
						header("Pragma: no-cache" );
					}

					$processing_time = microtime(true) - $start_time;
					header("X-Kaltura:cached-dispatcher,$key,$processing_time");
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
				header("Cache-Control: private, max-age=$max_age, max-stale=0");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . 'GMT');
				header("Content-Length: ".strlen($cachedResponse));
				echo $cachedResponse;
				die;
			}
			
			$cachedResponse = $cache->get("kwidget$uri");
			if ($cachedResponse)
			{
				header("X-Kaltura:cached-dispatcher");
				header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
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
				header("Cache-Control: public, max-age=$max_age, max-stale=0");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
				header('Last-Modified: Sun, 19 Nov 2000 08:52:00 GMT');
				header("Content-Length: $total_length ");
				header("Pragma:");
				header("Content-Type: $content_type");
				
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

// Logger
kLoggerCache::InitLogger('ps2', 'PS2');

sfLogger::getInstance()->registerLogger(KalturaLog::getInstance());
sfLogger::getInstance()->setLogLevel(7);
sfConfig::set('sf_logging_enabled', true);

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

ActKeyUtils::checkCurrent();
sfContext::getInstance()->getController()->dispatch();
