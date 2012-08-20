<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaEntryDistributionListResponse extends KalturaObject
{
	/**
	 * @var KalturaEntryDistributionArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}