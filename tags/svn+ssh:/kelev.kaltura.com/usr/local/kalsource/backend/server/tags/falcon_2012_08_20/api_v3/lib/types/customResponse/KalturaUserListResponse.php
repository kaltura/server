<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserListResponse extends KalturaObject
{
	/**
	 * @var KalturaUserArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}