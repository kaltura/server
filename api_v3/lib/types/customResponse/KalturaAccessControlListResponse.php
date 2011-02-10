<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlListResponse extends KalturaObject
{
	/**
	 * @var KalturaAccessControlArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}