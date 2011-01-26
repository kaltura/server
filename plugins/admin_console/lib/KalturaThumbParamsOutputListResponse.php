<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
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