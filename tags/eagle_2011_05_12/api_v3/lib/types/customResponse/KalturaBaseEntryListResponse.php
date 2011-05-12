<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntryListResponse extends KalturaObject
{
	/**
	 * @var KalturaBaseEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}