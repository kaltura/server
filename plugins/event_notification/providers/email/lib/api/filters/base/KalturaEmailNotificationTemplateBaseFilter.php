<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaEmailNotificationTemplateBaseFilter extends KalturaEventNotificationTemplateFilter
{
	private $map_between_objects = array
	(
	);

	private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}
}
