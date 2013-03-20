<?php

class kDeliveryUtils {
	
	private static $forceDeliveryTypeForTag = array('exclusive_widevine' => 'http');
	
	/*
	 * retrieves the streamer type for a delivery type array 
	 */
	public static function getStreamerType (array $deliveryType){
		if (isset($deliveryType['streamerType'])){
			if ($deliveryType['streamerType'] != PlaybackProtocol::AUTO){
				return $deliveryType['streamerType'];
			}
		}
		
		return PlaybackProtocol::HTTP;
	}
	/*
	 * retrieves the media protocol for a delivery type array
	 */
	public static function getMediaProtocol(array $deliveryType){
		if (isset($deliveryType['mediaProtocol'])){
				return $deliveryType['mediaProtocol'];
		}

		return PlaybackProtocol::HTTP;
	}
	
	public static function getForcedDeliveryTypeKey($tag)
	{
		if(!$tag)
			return null;
		if(array_key_exists($tag, self::$forceDeliveryTypeForTag))
			return self::$forceDeliveryTypeForTag[$tag];
		else
			return null;
		
	}
	
	public static function getDeliveryTypeFromConfig($key)
	{
		$playersConfig = kConf::getMap('players');
		if (!is_array($playersConfig))
		{
			KalturaLog::err('Players section is not defined');
			return null;
		}
		if(!isset($playersConfig['delivery_types']))
		{
			KalturaLog::err('Delivery types section is not defined');
			return null;
		}
		$deliveryTypeConfig = $playersConfig['delivery_types'];
		if (!isset($deliveryTypeConfig[$key]))
		{
			KalturaLog::err('The key '.$key.' was not found in the delivery types config section');
			return null;
		}

		return $deliveryTypeConfig[$key];
	}
}