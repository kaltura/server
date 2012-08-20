<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserLoginDataListResponse extends KalturaObject
{
	/**
	 * @var KalturaUserLoginDataArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}