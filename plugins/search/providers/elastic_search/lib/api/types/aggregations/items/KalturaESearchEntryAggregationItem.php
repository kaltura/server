<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */


class KalturaESearchEntryAggregationItem extends KalturaESearchAggregationItem
{

	/**
	 *  @var KalturaESearchEntryAggregateByFieldName
	 */
	public $fieldName;


	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchEntryAggregationItem();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return array (
			KalturaESearchEntryAggregateByFieldName::ENTRY_TYPE => ESearchEntryAggregationFieldName::ENTRY_TYPE,
			KalturaESearchEntryAggregateByFieldName::MEDIA_TYPE => ESearchEntryAggregationFieldName::MEDIA_TYPE,
			KalturaESearchEntryAggregateByFieldName::TAGS => ESearchEntryAggregationFieldName::TAGS,
			KalturaESearchEntryAggregateByFieldName::ACCESS_CONTROL_PROFILE => ESearchEntryAggregationFieldName::ACCESS_CONTROL_PROFILE);
	}

	public function coreToApiResponse($coreResponse, $fieldName = null)
	{
		$agg = new KalturaESearchAggregationResponseItem();
		$agg->fieldName = $fieldName;
		$agg->name = ESearchEntryAggregationItem::KEY;
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreResponse[ESearchAggregations::BUCKETS];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				$responseBucket = new KalturaESearchAggregationBucket();
				$responseBucket->fromArray($bucket);
				$bucketsArray[] = $responseBucket;
			}
		}
		$agg->buckets = $bucketsArray;
		return array($agg);
	}


}