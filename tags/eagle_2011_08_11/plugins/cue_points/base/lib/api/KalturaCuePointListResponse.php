<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.objects
 */
class KalturaCuePointListResponse extends KalturaObject
{
	/**
	 * @var KalturaCuePointArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}