<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryListResponse extends KalturaObject
{
	/**
	 * @var KalturaCategoryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}