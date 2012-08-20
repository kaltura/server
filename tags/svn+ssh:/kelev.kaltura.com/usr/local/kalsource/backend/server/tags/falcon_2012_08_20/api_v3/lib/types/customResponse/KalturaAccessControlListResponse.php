<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaAccessControlProfileListResponse instead
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