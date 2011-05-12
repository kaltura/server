<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerListResponse extends KalturaObject
{
	/**
	 * @var KalturaSchedulerArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}