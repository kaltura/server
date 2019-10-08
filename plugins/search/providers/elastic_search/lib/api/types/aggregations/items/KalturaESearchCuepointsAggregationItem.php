<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchCuepointsAggregationItem extends KalturaESearchAggregationItem
{
	/**
	 *  @var KalturaESearchCuePointAggregateByFieldName
	 */
	public $fieldName;

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchCuepointsAggregationItem();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return array(
			KalturaESearchCuePointAggregateByFieldName::TAGS => ESearchCuePointsAggregationFieldName::TAGS,
			KalturaESearchCuePointAggregateByFieldName::TYPE => ESearchCuePointsAggregationFieldName::TYPE);
	}

	public function coreToApiResponse($coreResponse, $fieldName = null)
	{
		$agg = new KalturaESearchAggregationResponseItem();
		$agg->fieldName = $fieldName;
		$agg->name = ESearchCuepointsAggregationItem::KEY;

		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreResponse[ESearchAggregationItem::NESTED_BUCKET][ESearchAggregations::BUCKETS];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				$responseBucket = new KalturaESearchAggregationBucket();
				$responseBucket->fromArray($bucket);
				if($fieldName === ESearchCuePointsAggregationFieldName::TYPE)
				{
					$responseBucket->value =  kPluginableEnumsManager::coreToApi('CuePointType' ,$responseBucket->value);
				}
				$bucketsArray[] = $responseBucket;
			}
		}
		$agg->buckets = $bucketsArray;
		return array($agg);
	}
}