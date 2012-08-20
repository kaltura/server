<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbParamsOutputListResponse extends KalturaObject
{
	/**
	 * @var KalturaThumbParamsOutputArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}