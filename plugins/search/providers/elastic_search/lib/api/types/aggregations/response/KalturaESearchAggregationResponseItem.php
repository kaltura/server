<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationResponseItem extends KalturaObject
{
	/**
	 * @var KalturaString
	 */
	public $name;

	/**
	 * @var KalturaString
	 */
	public $fieldName;

	/**
	 * @var KalturaESearchAggregationBucketsArray
	 */
	public $buckets;
}