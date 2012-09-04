<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVerizonVcastDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{
	static private $map_between_objects = array
	(
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), KalturaVerizonVcastDistributionProviderBaseFilter::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), KalturaVerizonVcastDistributionProviderBaseFilter::$order_by_map);
	}
}
