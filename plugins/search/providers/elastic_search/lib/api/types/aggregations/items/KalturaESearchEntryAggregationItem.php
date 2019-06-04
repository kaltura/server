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
				$bucketsArray[] = $reponseBucket;
			}
		}
		return $bucketsArray;
	}


}