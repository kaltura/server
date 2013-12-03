<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericListResponse extends KalturaObject
{
	/**
	 * @var KalturaGenericArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}