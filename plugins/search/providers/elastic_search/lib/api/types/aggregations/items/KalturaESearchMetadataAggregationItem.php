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

	protected function getMetadataFieldNameFromXpath($xpath)
	{
		$token = explode("'", $xpath);
		return $token[3];
	}

	public function coreToApiResponse($coreRespone)
	{
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreRespone[ESearchAggregationItem::NESTED_BUCKET][ESearchAggregations::BUCKETS];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				//get the field name from the xpath
				$metadataFieldName = $this->getMetadataFieldNameFromXpath($bucket[ESearchAggregations::KEY]);

				// loop over the subaggs
				$subBuckets = $bucket[ESearchMetadataAggregationItem::SUB_AGG][ESearchAggregations::BUCKETS];
				foreach($subBuckets as $subBucket)
				{
					$responseBucket = new KalturaESearchAggregationBucket();
					$responseBucket->value = $metadataFieldName.':'. $subBucket[ESearchAggregations::KEY];
					$responseBucket->count = $subBucket[ESearchAggregations::DOC_COUNT];
					$bucketsArray[] = $responseBucket;
				}

			}
		}
		return $bucketsArray;
	}


}