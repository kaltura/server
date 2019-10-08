<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationBucketsArray extends KalturaTypedArray
{
	public function __construct()
	{
	    parent::__construct("KalturaESearchAggregationBucket");
	}
}