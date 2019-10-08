<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */


class KalturaESearchCategoryAggregationItem extends KalturaESearchAggregationItem
{
	/**
	 *  @var KalturaESearchCategoryAggregateByFieldName
	 */
	public $fieldName;

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchCategoryAggregationItem();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return array (KalturaESearchCategoryAggregateByFieldName::CATEGORY_NAME => ESearchCategoryAggregationFieldName::CATEGORY_NAME);
	}

	public function coreToApiResponse($coreResponse, $fieldName = null)
	{
		$agg = new KalturaESearchAggregationResponseItem();
		$agg->fieldName = $fieldName;
		$agg->name = ESearchCategoryAggregationItem::KEY;
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreResponse[ESearchAggregations::BUCKETS];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				$responseBucket = new KalturaESearchAggregationBucket();
				$responseBucket->fromArray($bucket);
				list(,$categoryName) = elasticSearchUtils::reverseFormatCategoryNameStatus($responseBucket->value);
				$responseBucket->value = $categoryName;
				$bucketsArray[] = $responseBucket;
			}

		}
		$agg->buckets = $bucketsArray;
		return array($agg);
	}

}