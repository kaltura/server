<?php

/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaDeliveryFactory {
	
	public static function getCoreDeliveryInstanceByType($type) {
		$class = DeliveryPeer::getClassByDeliveryType($type);
		return new $class();
	}
	
	public static function getDeliveryInstanceByType($type) {
		switch ($type) {
			case DeliveryType::GENERIC_HLS:
				return new KalturaDeliveryGenericAppleHttp();
			case KalturaDeliveryType::GENERIC_HDS:
				return new KalturaDeliveryGenericHds();
			case KalturaDeliveryType::GENERIC_HTTP:
					return new KalturaDeliveryGenericHttp();
			case KalturaDeliveryType::RTMP:
				return new KalturaDeliveryRtmp();
			case KalturaDeliveryType::AKAMAI_HTTP:
				return new KalturaDeliveryAkamaiHttp();
			default:
				return new KalturaDelivery();
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
