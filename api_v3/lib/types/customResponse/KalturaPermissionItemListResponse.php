<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPremissionItemListResponse extends KalturaObject
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