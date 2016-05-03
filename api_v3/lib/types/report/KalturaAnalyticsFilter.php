<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAnalyticsFilter extends KalturaObject
{
	/**
	 * Query start time (in local time)
	 * @var string
	 */
	public $from_time;

	/**
	 * Query end time (in local time)
	 * @var string
	 */
	public $to_time;

	/**
	 * Comma separated metrics list
	 * @var string
	 */
	public $metrics;

	/**
	 * Timezone offset from UTC (in minutes)
	 * @var float
	 */
	public $utcOffset;

	/**
	 * Comma separated dimensions list
	 * @var string
	 */
	public $dimensions;

	/**
	 * Array of filters
	 * @var KalturaReportFilterArray
	 */
	public $filters;

	public function __construct() {
		$this->utcOffset = 0;
	}

	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$sourceObject->validatePropertyNotNull("from_time");
		$sourceObject->validatePropertyNotNull("to_time");
		$sourceObject->validatePropertyNotNull("metrics");
		return parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
}