<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.objects
 */
class KalturaESearchHistoryAggregationItem extends KalturaESearchAggregationItem
{
	/**
	 *  @var KalturaESearchHistoryAggregateFieldName
	 */
	public $fieldName;

	public function getFieldEnumMap()
	{
		return array (KalturaESearchHistoryAggregateFieldName::SEARCH_TERM => ESearchHistoryFieldName::SEARCH_TERM);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchHistoryAggregationItem();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function coreToApiResponse($coreResponse, $fieldName = null)
	{
		$agg = new KalturaESearchAggregationResponseItem();
		$agg->fieldName = $fieldName;
		$agg->name = ESearchHistoryAggregationItem::KEY;
		$agg->buckets = parent::coreBucketToApiResponse($coreResponse);
		return array($agg);
	}

}