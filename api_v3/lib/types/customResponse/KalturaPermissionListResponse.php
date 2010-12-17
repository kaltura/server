<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPermissionListResponse extends KalturaObject
{
	/**
	 * @var KalturaPermissionArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}