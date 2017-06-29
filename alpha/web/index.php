<?php
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Range, Cache-Control');
	header('Access-Control-Allow-Methods: POST, GET, HEAD, OPTIONS');
	header('Access-Control-Expose-Headers: Server, Content-Length, Content-Range, Date, Cache-Control, Content-Encoding');
	exit;
}

$start = microtime(true);
require_once(dirname(__FILE__).'/../config/kConf.php');

function sendCachingHeaders($max_age = 864000, $private = false, $last_modified = null)
{
	if ($max_age)
	{
		$cache_scope = $private ? "private" : "public";
		header("Cache-Control: $cache_scope, max-age=$max_age, max-stale=0");
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
		if ($last_modified)
			header('Last-modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
		else
			header('Last-Modified: Sun, 19 Nov 2000 08:52:00 GMT');
	}
	else
	{
		header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache" );
	}
}

function checkCache()
{
	$baseDir = "/tmp/cache_v2";

	$start_time = microtime(true);

	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
	$host = "";
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
		$host =  $_SERVER['HTTP_X_FORWARDED_HOST'];
	else if (isset($_SERVER['HTTP_HOST']))
		$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER["REQUEST_URI"];
	
	$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_APC_LCAL);
	if($cache)
	{
		$url = $cache->get("redirect-".$protocol.$uri);
		if ($url)
		{
			sendCachingHeaders(60, true, time());
			
			header("X-Kaltura:cached-dispatcher-redirect");
			header("Location:$url");
			die;
		}
		
		$errorHeaders = $cache->get("exterror-$protocol://$host$uri");
		if ($errorHeaders !== false)
		{
			sendCachingHeaders(60, true, time());
			
			foreach ($errorHeaders as $header)
			{
				header($header);
			}
			die;
		}
	}

	if (strpos($uri, "/playManifest") !== false)
	{
		require_once(dirname(__FILE__)."/../apps/kaltura/lib/cache/kPlayManifestCacher.php");
		$cache = kPlayManifestCacher::getInstance();
		$cache->checkOrStart();
	}	
	else if(strpos($uri, "/partnerservices2") !== false)
	{
		$params = $_GET + $_POST;
		unset($params['ks']);
		unset($params['kalsig']);
		$params['uri'] = $_SERVER['PATH_INFO'];
		$params['__protocol'] = $protocol;
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
						sendCachingHeaders($max_age, true, time());
					}
					else
					{
						sendCachingHeaders(0);
					}

					$processing_time = microtime(true) - $start_time;
					header("X-Kaltura:cached-dispatcher,$key,$processing_time");
					echo $response;
					die;
				}
			}
		}
	}
	else if (strpos($uri, "/kwidget") !== false)	
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		if ($cache)
		{
			// check if we cached the patched swf with flashvars
			$uri = $protocol.$uri;
			$cachedResponse = $cache->get("kwidgetswf$uri");
			if ($cachedResponse) // dont use cache if we want to force no caching
			{
				$max_age = 60 * 10;
				header("X-Kaltura:cached-dispatcher");
				header("Content-Type: application/x-shockwave-flash");
				sendCachingHeaders($max_age, true, time());
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
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		if ($cache)
		{
			require_once(dirname(__FILE__) . '/../apps/kaltura/lib/renderers/kRendererDumpFile.php');
			
			$cachedResponse = $cache->get("thumb$uri");
			if ($cachedResponse && is_array($cachedResponse))
			{
				list($renderer, $invalidationKey, $cacheTime) = $cachedResponse;
				
				$keysStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
				if ($keysStore)
				{
					$modifiedTime = $keysStore->get($invalidationKey);
					if ($modifiedTime && $modifiedTime > $cacheTime)
					{
						return;		// entry has changed (not necessarily the thumbnail)
					}
				}
				
				require_once(dirname(__FILE__) . '/../apps/kaltura/lib/monitor/KalturaMonitorClient.php');
				KalturaMonitorClient::initApiMonitor(true, 'extwidget.thumbnail', $renderer->partnerId);
				header("X-Kaltura:cached-dispatcher-thumb");
				$renderer->output();
				die;
			}
		}
	}	
	else if (strpos($uri, "/embedIframe/") !== false)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		if ($cache)
		{
			// check if we cached the patched swf with flashvars
			$cachedResponse = $cache->get("embedIframe$uri");
			if ($cachedResponse) // dont use cache if we want to force no caching
			{
				header("X-Kaltura:cached-dispatcher");
				sendCachingHeaders(0);
				header("Location:$cachedResponse");
				
				die;
			}
		}
	}
	else if (strpos($uri, "/serveFlavor/") !== false && $cache && $_SERVER["REQUEST_METHOD"] == "GET")
	{
		require_once(dirname(__FILE__) . '/../apps/kaltura/lib/renderers/kRendererDumpFile.php');
		require_once(dirname(__FILE__) . '/../apps/kaltura/lib/renderers/kRendererString.php');
		require_once(dirname(__FILE__) . '/../apps/kaltura/lib/monitor/KalturaMonitorClient.php');
		require_once(dirname(__FILE__) . '/../apps/kaltura/lib/request/kIpAddressUtils.php');
		
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
		$cacheKey = 'dumpFile-'.kIpAddressUtils::isInternalIp($_SERVER['REMOTE_ADDR']).'-'.$host.$uri;
		
		
		$renderer = $cache ? $cache->get($cacheKey) : null;
		if ($renderer)
		{
			KalturaMonitorClient::initApiMonitor(true, 'extwidget.serveFlavor', $renderer->partnerId);
			header("X-Kaltura:cached-dispatcher");
			$renderer->output();
			die;
		}
	}
}

checkCache();

define('KALTURA_LOG', 		'ps2');
define('SF_ENVIRONMENT',	'prod');
define('SF_DEBUG',			false);

require_once(__DIR__ . '/../bootstrap.php');

