<?php

class kDeliveryUtils {
	/*
	 * retrieves the streamer type for a delivery type array 
	 */
	public static function getStreamerType (array $deliveryType){
		if (isset($deliveryType['flashvars']) && isset($deliveryType['flashvars']['streamerType'])){
			if ($deliveryType['flashvars']['streamerType'] != PlaybackProtocol::AUTO){
				return $deliveryType['flashvars']['streamerType'];
			}
		}
		
		return PlaybackProtocol::HTTP;
	}
	/*
	 * retrieves the media protocol for a delivery type array
	 */
	public static function getMediaProtocol(array $deliveryType){
		if (isset($deliveryType['flashvars']) && isset($deliveryType['flashvars']['mediaProtocol'])){
				return $deliveryType['flashvars']['mediaProtocol'];
		}

		return PlaybackProtocol::HTTP;
	}
}