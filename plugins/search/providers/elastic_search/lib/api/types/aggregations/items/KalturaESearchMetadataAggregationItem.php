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

	public function coreToApiResponse($coreResponse, $fieldName=null)
	{
		$ret = array();
		$bucketsArray = new KalturaESearchAggregationBucketsArray();
		$buckets = $coreResponse[ESearchAggregationItem::NESTED_BUCKET][ESearchAggregations::BUCKETS];
		if ($buckets)
		{
			foreach ($buckets as $bucket)
			{
				$agg = new KalturaESearchAggregationResponseItem();
				$agg->name = ESearchMetadataAggregationItem::KEY;

				//get the field name from the xpath
				$metadataFieldName = $this->getMetadataFieldNameFromXpath($bucket[ESearchAggregations::KEY]);
				$agg->fieldName = $metadataFieldName;
				// loop over the subaggs
				$subBuckets = $bucket[ESearchMetadataAggregationItem::SUB_AGG][ESearchAggregations::BUCKETS];
				foreach($subBuckets as $subBucket)
				{
					$responseBucket = new KalturaESearchAggregationBucket();
					$responseBucket->fromArray($subBucket);
					$bucketsArray[] = $responseBucket;
				}
				$agg->buckets = $bucketsArray;
				$ret[] = $agg;
			}
		}
		return $ret;
	}


}