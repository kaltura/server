<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaGenericDistributionProviderActionListResponse extends KalturaObject
{
	/**
	 * @var KalturaGenericDistributionProviderActionArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}