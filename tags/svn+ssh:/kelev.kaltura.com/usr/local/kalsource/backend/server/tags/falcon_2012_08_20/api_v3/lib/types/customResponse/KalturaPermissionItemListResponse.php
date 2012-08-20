<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPermissionItemListResponse extends KalturaObject
{
	/**
	 * @var KalturaPermissionItemArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}