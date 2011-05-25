<?php
/**
 * @package api
 * @subpackage objects
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