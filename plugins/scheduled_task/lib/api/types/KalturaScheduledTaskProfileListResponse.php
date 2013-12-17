<?php
/**
 * @package plugins.scheduledTask
 * @subpackage api.objects
 */
class KalturaScheduledTaskProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaScheduledTaskProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}