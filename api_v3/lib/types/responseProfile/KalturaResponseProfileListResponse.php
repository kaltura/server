<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaResponseProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}