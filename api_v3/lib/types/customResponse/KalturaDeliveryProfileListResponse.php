<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileListResponse extends KalturaObject
{
	/**
	 * @var KalturaDeliveryProfileArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}