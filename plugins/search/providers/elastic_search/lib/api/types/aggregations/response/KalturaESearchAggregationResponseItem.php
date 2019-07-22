<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationResponseItem extends KalturaObject
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $fieldName;

	/**
	 * @var KalturaESearchAggregationBucketsArray
	 */
	public $buckets;
}