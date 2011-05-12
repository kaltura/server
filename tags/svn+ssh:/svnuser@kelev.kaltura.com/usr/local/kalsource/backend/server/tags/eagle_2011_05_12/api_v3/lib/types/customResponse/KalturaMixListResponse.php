<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMixListResponse extends KalturaObject
{
	/**
	 * @var KalturaMixEntryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}