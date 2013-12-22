<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaObjectListResponse extends KalturaObject
{
	/**
	 * @var KalturaObjectArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}