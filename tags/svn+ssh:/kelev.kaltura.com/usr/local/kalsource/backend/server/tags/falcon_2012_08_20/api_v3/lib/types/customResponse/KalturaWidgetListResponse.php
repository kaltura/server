<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaWidgetListResponse extends KalturaObject
{
	/**
	 * @var KalturaWidgetArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}