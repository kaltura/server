<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */

class KalturaReachReportInputFilter extends KalturaReportInputFilter
{

	private static $map_between_objects = array
	(
		'serviceType',
		'serviceFeature',
		'turnAroundTime',
	);

	/**
	 * @var KalturaVendorServiceType
	 */
	public $serviceType;
	
	/**
	 * @var KalturaVendorServiceFeature
	 */
	public $serviceFeature;
	
	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTime;

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toReportsInputFilter($reportInputFilter = null)
	{
		if (!$reportInputFilter)
			$reportInputFilter = new reachReportsInputFilter();

		return parent::toReportsInputFilter($reportInputFilter);
	}
}