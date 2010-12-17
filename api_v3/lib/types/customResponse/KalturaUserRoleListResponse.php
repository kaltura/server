<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserRoleListResponse extends KalturaObject
{
	/**
	 * @var KalturaUserRoleArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}