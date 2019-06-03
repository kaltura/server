<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchAggregationResponse extends KalturaObject
{
	protected function getApiObjects($aggregationName)
	{
		return explode(':', $aggregationName);
	}

	public function resultToApi($aggregationResults)
	{
		$aggs = new KalturaESearchAggregationResponseArray();
		foreach ($aggregationResults as $key=>$response)
		{
			list ($responseObject, $fieldName) = $this->getApiObjects($key);
			$agg = new KalturaESearchAggregationResponseItem();
			$agg->fieldName = $fieldName;
			$agg->name = $responseObject;
			$buckets=null;
			if(isset($response['buckets']))
			{
				$buckets = $response['buckets'];
			}
			elseif (isset($response['NestedBucket']))
			{
				$buckets = $response['NestedBucket']['buckets'];
			}
			if ($buckets)
			{
				$agg->buckets = new KalturaESearchAggregationBucketsArray();
				foreach ($buckets as $bucket)
				{
					$reponseBucket = new KalturaESearchAggregationBucket();
					$reponseBucket->fromArray($bucket);
					$agg->buckets[] = $reponseBucket;
				}
			}
			$aggs[] = $agg;
		}
		return $aggs;
	}

}