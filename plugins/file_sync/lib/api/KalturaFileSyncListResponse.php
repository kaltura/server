<?php
/**
 * @package plugins.fileSync
 * @subpackage api.objects
 */
class KalturaFileSyncListResponse extends KalturaObject
{
	/**
	 * @var KalturaFileSyncArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}