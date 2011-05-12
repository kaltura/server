<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaListResponse extends KalturaObject
{
	/**
	 * @var KalturaMediaEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}