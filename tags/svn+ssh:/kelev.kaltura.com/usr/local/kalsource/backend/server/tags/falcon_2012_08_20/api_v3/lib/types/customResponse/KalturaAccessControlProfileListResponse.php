<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaAccessControlProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}