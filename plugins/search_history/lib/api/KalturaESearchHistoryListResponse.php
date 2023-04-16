<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.objects
 */
class KalturaESearchHistoryListResponse extends KalturaListResponse
{
    /**
     * @var KalturaESearchHistoryArray
     * @readonly
     */
    public $objects;

	/**
	 * @var KalturaESearchAggregationResponseArray
	 * @readonly
	 */
	public $aggregations;
}
