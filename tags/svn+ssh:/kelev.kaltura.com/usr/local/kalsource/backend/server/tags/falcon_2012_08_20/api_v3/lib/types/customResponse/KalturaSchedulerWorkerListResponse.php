<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerWorkerListResponse extends KalturaObject
{
	/**
	 * @var KalturaSchedulerWorkerArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}