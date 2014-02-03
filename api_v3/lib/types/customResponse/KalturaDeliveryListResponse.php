<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryListResponse extends KalturaObject
{
	/**
	 * @var KalturaDeliveryArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}