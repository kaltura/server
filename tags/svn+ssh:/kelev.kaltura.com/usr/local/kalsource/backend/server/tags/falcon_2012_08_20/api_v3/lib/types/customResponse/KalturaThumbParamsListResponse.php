<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbParamsListResponse extends KalturaObject
{
	/**
	 * @var KalturaThumbParamsArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}