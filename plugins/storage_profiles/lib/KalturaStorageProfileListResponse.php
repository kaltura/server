<?php
/**
 * @package plugins.storageProfile
 * @subpackage api.objects
 */
class KalturaStorageProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaStorageProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}