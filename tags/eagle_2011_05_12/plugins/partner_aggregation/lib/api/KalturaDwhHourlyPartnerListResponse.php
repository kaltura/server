<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.objects
 */
class KalturaDwhHourlyPartnerListResponse extends KalturaObject
{
	/**
	 * @var KalturaDwhHourlyPartnerArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}