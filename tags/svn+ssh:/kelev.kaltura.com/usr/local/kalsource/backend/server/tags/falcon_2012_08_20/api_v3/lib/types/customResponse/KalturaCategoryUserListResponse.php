<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryUserListResponse extends KalturaObject
{
	/**
	 * @var KalturaCategoryUserArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}