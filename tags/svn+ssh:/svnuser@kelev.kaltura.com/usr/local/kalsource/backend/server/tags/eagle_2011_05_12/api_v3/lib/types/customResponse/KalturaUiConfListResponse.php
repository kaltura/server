<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUiConfListResponse extends KalturaObject
{
	/**
	 * @var KalturaUiConfArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}