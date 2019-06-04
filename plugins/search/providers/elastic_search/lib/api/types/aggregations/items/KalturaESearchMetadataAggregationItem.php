<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */


class KalturaESearchMetadataAggregationItem extends KalturaESearchAggregationItem
{
	/**
	 *  @var KalturaESearchMetadataAggregateByFieldName
	 */
	public $fieldName;

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchMetadataAggregationItem();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return array ();
	}

	public function coreToApiResponse($coreRespone)
	{
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreRespone['NestedBucket']['buckets'];
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