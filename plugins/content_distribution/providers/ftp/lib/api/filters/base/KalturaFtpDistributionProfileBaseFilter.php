<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaFtpDistributionProfileBaseFilter extends KalturaConfigurableDistributionProfileFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaFtpDistributionProfileBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaFtpDistributionProfileBaseFilter::$order_by_map);
	}
}
