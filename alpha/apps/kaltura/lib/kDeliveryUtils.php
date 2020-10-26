<?php

class kDeliveryUtils {
	
	/*
	 * retrieves the streamer type for a delivery type array 
	 */
	public static function getStreamerType (array $deliveryType)
	{
		if ($deliveryType && isset($deliveryType['streamerType']) && $deliveryType['streamerType'] != PlaybackProtocol::AUTO)
			return $deliveryType['streamerType'];
		
		return PlaybackProtocol::HTTP;
	}
	/*
	 * retrieves the media protocol for a delivery type array
	 */
	public static function getMediaProtocol(array $deliveryType){
		if ($deliveryType && isset($deliveryType['mediaProtocol']))
		{
				return $deliveryType['mediaProtocol'];
		}

		return PlaybackProtocol::HTTP;
	}
	
	public static function getForcedDeliveryTypeKey($tag)
	{
		if($tag)
			return self::getForcedDeliveryTypeFromConfig($tag);
		else
			return null;
		
	}
	
	public static function getDeliveryTypeFromConfig($key)
	{
		$playersConfig = kConf::getMap('players');
		if (!is_array($playersConfig))
		{
			KalturaLog::err('Players section is not defined');
			return array();
		}
		if(!isset($playersConfig['delivery_types']))
		{
			KalturaLog::err('Delivery types section is not defined');
			return array();
		}
		$deliveryTypeConfig = $playersConfig['delivery_types'];
		if (!isset($deliveryTypeConfig[$key]))
		{
			KalturaLog::err('The key '.$key.' was not found in the delivery types config section');
			return array();
		}

		return $deliveryTypeConfig[$key];
	}
	
	public static function getForcedDeliveryTypeFromConfig($key)
	{
		$playersConfig = kConf::getMap('players');
		if (is_array($playersConfig) && isset($playersConfig['forced_delivery_types']))
		{
			$deliveryTypeConfig = $playersConfig['forced_delivery_types'];
			if (isset($deliveryTypeConfig[$key]))
				return $deliveryTypeConfig[$key];
		}
		return null;
	}
	
	public static function formatGenericUrl($url, $pattern = null, DeliveryProfileDynamicAttributes $params) {
		if ($pattern)
		{
			$seekFromSec = $params->getSeekFromTime() > 0 ? $params->getSeekFromTime() / 1000 : 0;
			$pattern = str_replace('{url}', $url, $pattern);
			$pattern = str_replace('{seekFromSec}', $seekFromSec, $pattern);
			return $pattern;
		}
		else
		{
			return '/'.$url; // the trailing slash will force adding the host name to the url
		}
	}
	
	public static function addQueryParameter($url, $parameter) {
		$parsedUrl = parse_url($url);
		if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
			$url .= '&' . $parameter;
		else
			$url .= '?' . $parameter;
		
		return $url;
	}

	/**
	 * Extract all non-empty / non-comment lines from a .m3u/.m3u8 content
	 * @param $content array|string Full file content as a single string or as a lines-array
	 * @return array Valid lines
	 */
	public static function getM3U8Urls( $content )
	{
		$outLines = array();
	
		$lines = is_array($content) ? $content : explode("\n", trim($content));

		if (trim(reset($lines)) != '#EXTM3U')
		{
			return $outLines;
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

	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
}