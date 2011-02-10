<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerListResponse extends KalturaObject
{
	/**
	 * @var KalturaPartnerArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}