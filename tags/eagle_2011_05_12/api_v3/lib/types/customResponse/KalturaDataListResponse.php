<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDataListResponse extends KalturaObject
{
	/**
	 * @var KalturaDataEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}