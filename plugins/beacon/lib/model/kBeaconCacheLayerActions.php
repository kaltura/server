<?php
/**
 * Class kBeaconCacheLayerActions
 *
 * Package and location is not indicated
 * Should not include any kaltura dependency in this class - to enable it to run in cache only mode
 */


require_once (dirname(__FILE__) . '/kBeacon.php');
require_once (dirname(__FILE__) . '/../../../../plugins/beacon/lib/model/enums/BeaconIndexType.php');
require_once (dirname(__FILE__) . '/../../../../plugins/beacon/lib/model/enums/BeaconObjectTypes.php');
require_once (dirname(__FILE__) . '/../../../../plugins/beacon/lib/model/kBeaconSearchQueryManger.php');

require_once (dirname(__FILE__) . '/../../../../plugins/queue/lib/QueueProvider.php');
require_once (dirname(__FILE__) . '/../../../../plugins/queue/providers/rabbit_mq/lib/RabbitMQProvider.php');
require_once (dirname(__FILE__) . '/../../../../plugins/queue/providers/rabbit_mq/lib/MultiCentersRabbitMQProvider.php');


class kBeaconCacheLayerActions
{
	const PARAM_EVENT_TYPE = "beacon:eventType";
	const PARAM_OBJECT_ID = "beacon:objectId";
	const PARAM_RELATED_OBJECT_TYPE = "beacon:relatedObjectType";
	const PARAM_PRIVATE_DATA = "beacon:privateData";
	const PARAM_RAW_DATA = "beacon:rawData";
	const PARAM_SHOULD_LOG = "shouldLog";
	const PARAM_KS_PARTNER_ID = "___cache___partnerId";
	const PARAM_IMPERSONATED_PARTNER_ID = "partnerId";
	
	public static function validateInputExists($params, $paramKey)
	{
		return !array_key_exists($paramKey, $params) || $params[$paramKey] == '';
	}
	
	public static function add($params)
	{
		if(is_null($params))
			throw new Exception("Params not provided");
		
		if(self::validateInputExists($params, kBeaconCacheLayerActions::PARAM_KS_PARTNER_ID))
			return false;
		
		if (self::validateInputExists($params, kBeaconCacheLayerActions::PARAM_EVENT_TYPE) ||
			self::validateInputExists($params, kBeaconCacheLayerActions::PARAM_OBJECT_ID) ||
			self::validateInputExists($params, kBeaconCacheLayerActions::PARAM_RELATED_OBJECT_TYPE)
		)
			return false;
		
		$partnerId =  $params[kBeaconCacheLayerActions::PARAM_KS_PARTNER_ID];
		if(isset($params[kBeaconCacheLayerActions::PARAM_IMPERSONATED_PARTNER_ID]))
			$partnerId = $params[kBeaconCacheLayerActions::PARAM_IMPERSONATED_PARTNER_ID];
		
		if(!$partnerId)
			return false;
		
		$beacon = new kBeacon($partnerId);
		$beacon->setObjectId($params[kBeaconCacheLayerActions::PARAM_OBJECT_ID]);
		$beacon->setEventType($params[kBeaconCacheLayerActions::PARAM_EVENT_TYPE]);
		$beacon->setRelatedObjectType($params[kBeaconCacheLayerActions::PARAM_RELATED_OBJECT_TYPE]);
		
		if(isset($params[kBeaconCacheLayerActions::PARAM_PRIVATE_DATA]))
			$beacon->setPrivateData($params[kBeaconCacheLayerActions::PARAM_PRIVATE_DATA]);
		
		if(isset($params[kBeaconCacheLayerActions::PARAM_RAW_DATA]))
			$beacon->setRawData($params[kBeaconCacheLayerActions::PARAM_RAW_DATA]);
		
		$shouldLog = false;
		if(isset($params[kBeaconCacheLayerActions::PARAM_SHOULD_LOG]) && $params[kBeaconCacheLayerActions::PARAM_SHOULD_LOG])
			$shouldLog = true;
		
		$queueProvider = self::loadQueueProvider();
		if(!$queueProvider)
			throw new Exception("Queue Provider could not be initialized");
		
		return $beacon->index($shouldLog, $queueProvider);
	}
	
	public static function loadQueueProvider()
	{
		$constructorArgs = array();
		$constructorArgs['exchangeName'] = kBeacon::BEACONS_EXCHANGE_NAME;
		if(!kConf::hasMap('rabbit_mq'))
		{
			return null;
		}
		
		$rabbitConfig = kConf::getMap('rabbit_mq');
		if(isset($rabbitConfig['multiple_dcs']) && $rabbitConfig['multiple_dcs'])
		{
			return new MultiCentersRabbitMQProvider($rabbitConfig, $constructorArgs);
		}
		
		return new RabbitMQProvider($rabbitConfig, $constructorArgs);
	}
}
