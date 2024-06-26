<?php

/**
 * @package plugins.kafkaNotification
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaKafkaNotificationTemplateBaseFilter extends KalturaEventNotificationTemplateFilter
{
	static private $map_between_objects = array
	();
	
	static private $order_by_map = array
	();
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
}
