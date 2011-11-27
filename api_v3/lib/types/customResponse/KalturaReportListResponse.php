<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportListResponse extends KalturaObject
{
	/**
	 * @var KalturaReportArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}