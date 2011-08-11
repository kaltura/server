<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaCaptionParamsListResponse extends KalturaObject
{
	/**
	 * @var KalturaCaptionParamsArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}