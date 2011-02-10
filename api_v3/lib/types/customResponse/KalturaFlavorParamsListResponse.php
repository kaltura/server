<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParamsListResponse extends KalturaObject
{
	/**
	 * @var KalturaFlavorParamsArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}