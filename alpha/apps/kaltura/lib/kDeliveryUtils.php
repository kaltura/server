<?php

class kDeliveryUtils {
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
}