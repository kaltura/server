<?php

/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaDeliveryProfileFactory {
	
	public static function getCoreDeliveryProfileInstanceByType($type) {
		$class = DeliveryProfilePeer::getClassByDeliveryProfileType($type);
		return new $class();
	}
	
	public static function getDeliveryProfileInstanceByType($type) {
		switch ($type) {
			case KalturaDeliveryProfileType::GENERIC_HLS:
				return new KalturaDeliveryProfileGenericAppleHttp();
			case KalturaDeliveryProfileType::GENERIC_HDS:
				return new KalturaDeliveryProfileGenericHds();
			case KalturaDeliveryProfileType::GENERIC_HTTP:
					return new KalturaDeliveryProfileGenericHttp();
			case KalturaDeliveryProfileType::RTMP:
				return new KalturaDeliveryProfileRtmp();
			case KalturaDeliveryProfileType::AKAMAI_HTTP:
				return new KalturaDeliveryProfileAkamaiHttp();
			default:
				return new KalturaDeliveryProfile();
		}
	}
	
	public static function getTokenizerInstanceByType($type) {
		switch ($type) {
			case 'kLevel3UrlTokenizer':
				return new KalturaUrlTokenizerLevel3();
			case 'kLimeLightUrlTokenizer':
				return new KalturaUrlTokenizerLimeLight();
			// Add other tokenizers here
			default:
				$apiObject = KalturaPluginManager::loadObject('KalturaTokenizer', $type);
				if($apiObject)
					return $apiObject;
				KalturaLog::err("Cannot load API object for core Tokenizer [" . $type . "]");
				return null;
		}
	}
	
	public static function getRecognizerByType($type) {
		switch ($type) {
			case 'kUrlRecognizerAkamaiG2O':
				return new KalturaUrlRecognizerAkamaiG2O();
				break;
			case 'kUrlRecognizer':
				return new KalturaUrlRecognizer();
			default:
				$apiObject = KalturaPluginManager::loadObject('KalturaRecognizer', $type);
				if($apiObject)
					return $apiObject;
				KalturaLog::err("Cannot load API object for core Recognizer [" . $type . "]");
				return null;
		}
	}

}
