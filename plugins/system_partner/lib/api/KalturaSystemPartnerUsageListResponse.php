<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerUsageListResponse extends KalturaObject
{
	/**
	 * @var KalturaSystemPartnerUsageArray
	 */
	public $objects;

	/**
	 * @var int
	 */
	public $totalCount;
}