<?php
/**
 * @package plugins.ftpDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaFtpScheduledDistributionProviderBaseFilter extends KalturaFtpDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaFtpScheduledDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaFtpScheduledDistributionProviderBaseFilter::$order_by_map);
	}
}
