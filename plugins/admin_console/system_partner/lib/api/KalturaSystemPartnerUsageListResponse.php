<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerUsageListResponse extends KalturaListResponse
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