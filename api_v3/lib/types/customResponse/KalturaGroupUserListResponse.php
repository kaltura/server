<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGroupUserListResponse extends KalturaObject
{
	/**
	 * @var KalturaGroupUserArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}