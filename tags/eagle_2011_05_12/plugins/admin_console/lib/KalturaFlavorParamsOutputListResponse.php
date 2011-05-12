<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
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