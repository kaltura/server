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

	protected function fixCategoryName($coreName)
	{

	}

	public function coreToApiResponse($coreRespone)
	{
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreRespone['buckets'];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				$reponseBucket = new KalturaESearchAggregationBucket();
				$reponseBucket->fromArray($bucket);
				list(,$categoryName) = elasticSearchUtils::reverseFormatCategoryNameStatus($reponseBucket->value);
				$reponseBucket->value = $categoryName;
				$bucketsArray[] = $reponseBucket;
			}

		}
		return $bucketsArray;
	}

}