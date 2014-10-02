<?php

/**
 * Description:	Check if the given entry ID at the given host is live
 * Usage:		php checkIsLive.php <entryId> <host> [debug]
 * Output:		Writes the result of the test to stdout
 * Exit codes:	0 = Entry is live
 * 				1 = Entry is not live
 * 				2 = Input error 
 */

///////////////////////////////////////////////////////////////
// KalturaLog
///////////////////////////////////////////////////////////////
class KalturaLog {
	private static $enabled = false;
	
	public static function enable( $enabled ) { self::$enabled = $enabled; }
	public static function debug( $s ) { self::writeLine("DEBUG", $s); }
	public static function log( $s ) { self::writeLine("LOG", $s); }
	public static function info( $s ) { self::writeLine("INFO", $s); }
	public static function err( $s ) { self::writeLine("ERR", $s); }

	private static function writeLine( $level, $s ) {
		if ( self::$enabled ) {
			echo date("Y-M-d H:i:s") . "\t$level\t$s\n";
		}
	}
}

///////////////////////////////////////////////////////////////
// kConf
///////////////////////////////////////////////////////////////
class kConf {
	public static function get($paramName, $mapName = 'local', $defaultValue = false)
	{
		return array(
				'application/vnd.apple.mpegurl',
				'application/x-mpegURL',
				'audio/x-mpegURL',
			);
	}
}

///////////////////////////////////////////////////////////////
// DeliveryProfileLive
///////////////////////////////////////////////////////////////
abstract class DeliveryProfileLive {

	/**
	 * Method checks whether the URL passed to it as a parameter returns a response.
	 * @param string $url
	 * @return string
	 */
	protected function urlExists ($url, array $contentTypeToReturn, $range = null)
	{
		if (is_null($url))
			return false;
		if (!function_exists('curl_init'))
		{
			KalturaLog::err('Unable to use util when php curl is not enabled');
			return false;
		}
		KalturaLog::log("Checking URL [$url] with range [$range]");
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (!is_null($range))
		{
			curl_setopt($ch, CURLOPT_RANGE, $range);
		}
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);

		$contentTypeToCheck = strstr($contentType, ";", true);
		if(!$contentTypeToCheck)
			$contentTypeToCheck = $contentType;
		if($data && $httpcode>=200 && $httpcode<300)
		{
			return in_array(trim($contentTypeToCheck), $contentTypeToReturn) ? $data : true;
		}
		else
			return false;
	}

	/**
	 * Function check if URL provided is a valid one if not returns fixed url with the parent url relative path
	 * @param string $urlToCheck
	 * @param string $parentURL
	 * @return fixed url path
	 */
	protected function checkIfValidUrl($urlToCheck, $parentURL)
	{
		$urlToCheck = trim($urlToCheck);
		if (strpos($urlToCheck, '://') === false)
		{
			$urlToCheck = dirname($parentURL) . '/' . $urlToCheck;
		}

		return $urlToCheck;
	}
}

///////////////////////////////////////////////////////////////
// DeliveryProfileLiveAppleHttp
///////////////////////////////////////////////////////////////
class DeliveryProfileLiveAppleHttp extends DeliveryProfileLive {

	const HLS_LIVE_STREAM_CONTENT_TYPE = "hls_live_stream_content_type";
	const M3U8_MASTER_PLAYLIST_IDENTIFIER = "EXT-X-STREAM-INF";
	const MAX_IS_LIVE_ATTEMPTS = 3;

	public function checkIsLive( $url )
	{
		$urlContent = $this->urlExists($url, kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE));
		if( ! $urlContent )
		{
			return false;
		}

		if ( strpos( $urlContent, self::M3U8_MASTER_PLAYLIST_IDENTIFIER ) !== false )
		{
			$isLive = $this->checkIsLiveMasterPlaylist( $url, $urlContent );
		}
		else
		{
			$isLive = $this->checkIsLiveMediaPlaylist( $url, $urlContent );
		}

		return $isLive;
	}

	/**
	 * Extract all non-empty / non-comment lines from a .m3u/.m3u8 content
	 * @param $content array|string Full file content as a single string or as a lines-array
	 * @return array Valid lines
	 */
	protected function getM3U8Urls( $content )
	{
		$outLines = array();

		if ( !$content )
		{
			return $outLines;
		}

		if ( !is_array($content) )
		{
			$lines = explode("\n", trim($content));
		}

		foreach ( $lines as $line )
		{
			$line = trim($line);
			if (!$line || $line[0] == '#')
			{
				continue;
			}

			$outLines[] = $line;
		}

		return $outLines;
	}

	/**
	 * Check if the given URL contains live entries (typically live .m3u8 URLs)
	 * @param string $url
	 * @param string|array $urlContent The URL's parsed content
	 * @return boolean
	 */
	protected function checkIsLiveMasterPlaylist( $url, $urlContent )
	{
		$lines = $this->getM3U8Urls( $urlContent );

		foreach ($lines as $urlLine)
		{
			$mediaUrl = $this->checkIfValidUrl($urlLine, $url);

			$urlContent = $this->urlExists($mediaUrl, kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE));

			if (!$urlContent)
			{
				continue;
			}

			$isLive = $this->checkIsLiveMediaPlaylist($mediaUrl, $urlContent);
			if ( $isLive )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if the given URL contains live entries (typically containing .ts URLs)
	 * @param string $url
	 * @param string|array $urlContent The URL's parsed content
	 * @return boolean
	 */
	protected function checkIsLiveMediaPlaylist( $url, $urlContent )
	{
		$lines = $this->getM3U8Urls( $urlContent );

		$lines = array_slice($lines, -self::MAX_IS_LIVE_ATTEMPTS, self::MAX_IS_LIVE_ATTEMPTS, true);
		foreach ($lines as $urlLine)
		{
			$tsUrl = $this->checkIfValidUrl($urlLine, $url);
			if ($this->urlExists($tsUrl ,kConf::get(self::HLS_LIVE_STREAM_CONTENT_TYPE),'0-1') !== false)
			{
				KalturaLog::log("Live ts url: $tsUrl");
				return true;
			}
		}

		return false;
	}
}

///////////////////////////////////////////////////////////////
// Main
///////////////////////////////////////////////////////////////
if ( count($argv) < 3 )
{
	echo "checkIsLive: Wrong number of input args." . PHP_EOL;
	exit( 2 );
}

$entryId = $argv[1];
$host = $argv[2];

$enableLog = false;
if ( isset( $argv[3] ) )
{
	$enableLog = ($argv[3] == 'debug') ? true : false;
}
KalturaLog::enable( $enableLog );

// Note: "/p/0" is good enough for checking if the url is live
$url = "http://$host/p/0/playManifest/entryId/$entryId/format/applehttp/protocol/http/b.m3u8/?rnd=" . time();

$monitor = new DeliveryProfileLiveAppleHttp();
$isLive = $monitor->checkIsLive($url);

echo "Entry [$entryId] on host [$host] is " . ($isLive ? "live" : "offline") . PHP_EOL;

exit( $isLive ? 0 : 1 );
