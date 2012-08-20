<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryEntryListResponse extends KalturaObject
{
	/**
	 * @var KalturaCategoryEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}