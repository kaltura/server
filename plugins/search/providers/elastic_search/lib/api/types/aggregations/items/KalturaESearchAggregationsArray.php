<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchAggregationsArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaESearchAggregationItem");
	}
}
