<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaGenericDistributionProviderListResponse extends KalturaObject
{
	/**
	 * @var KalturaGenericDistributionProviderArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}