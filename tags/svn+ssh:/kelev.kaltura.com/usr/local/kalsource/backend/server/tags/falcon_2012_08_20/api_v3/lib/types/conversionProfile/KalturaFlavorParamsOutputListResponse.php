<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorParamsOutputListResponse extends KalturaObject
{
	/**
	 * @var KalturaFlavorParamsOutputArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}