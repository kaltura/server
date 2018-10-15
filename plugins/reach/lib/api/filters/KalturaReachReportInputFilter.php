<?php

/**
 * @package plugins.reach
 * @subpackage api.filters
 */

class KalturaReachReportInputFilter extends KalturaReportInputFilter
{
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


	public function toReportsInputFilter ($reportsInputFilter = null)
	{
		$reachReportsInputFilter = new reachReportsInputFilter();
		parent::toReportsInputFilter($reachReportsInputFilter);
		$reachReportsInputFilter->serviceFeature = $this->serviceFeature;
		$reachReportsInputFilter->serviceType = $this->serviceType;
		$reachReportsInputFilter->turnAroundTime = $this->turnAroundTime;

		return $reachReportsInputFilter;
	}

	public function fromReportsInputFilter (  $reachReportsInputFilter )
	{
		parent::fromReportsInputFilter($reachReportsInputFilter);
		$this->serviceFeature = $reachReportsInputFilter->serviceFeature;
		$this->serviceType = $reachReportsInputFilter->serviceType;
		$this->turnAroundTime = $reachReportsInputFilter->turnAroundTime;
		return $this;
	}
}