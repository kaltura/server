<?php

/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaDeliveryFactory {
	
	public static function getDeliveryInstanceByType($type) {
		switch ($type) {
			case KalturaDeliveryType::AKAMAI_HTTP:
				return new KalturaDeliveryAkamaiHttp();
			case KalturaDeliveryType::AKAMAI_RTSP:
				return new KalturaDeliveryAkamaiRtsp();
			case KalturaDeliveryType::AKAMAI_RTMP:
				return new KalturaDeliveryRtmp();
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
			// TODO @_!! Add more here
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
