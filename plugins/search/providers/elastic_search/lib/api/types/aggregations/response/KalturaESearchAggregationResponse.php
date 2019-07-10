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

	protected function mapAggregationCoreObjects($coreObject)
	{
		$map = array(ESearchCategoryAggregationItem::KEY => 'KalturaESearchCategoryAggregationItem',
					 ESearchCuepointsAggregationItem::KEY => 'KalturaESearchCuepointsAggregationItem',
					 ESearchMetadataAggregationItem::KEY => 'KalturaESearchMetadataAggregationItem',
					 ESearchEntryAggregationItem::KEY => 'KalturaESearchEntryAggregationItem');
		$ret = isset($map[$coreObject]) ? $map[$coreObject] : null;
		return $ret;
	}

	public function resultToApi($aggregationResults)
	{
		$aggs = new KalturaESearchAggregationResponseArray();
		foreach ($aggregationResults as $key=>$response)
		{
			list ($responseObject, $fieldName) = $this->getApiObjects($key);
			$itemObjectName = $this->mapAggregationCoreObjects($responseObject);
			if(!$itemObjectName)
			{
				continue;
			}
			$objectItemHandler = new $itemObjectName();
			$aggsResponses = $objectItemHandler->coreToApiResponse($response, $fieldName);
			foreach ($aggsResponses as $aggsResponse)
			{
				$aggs[] = $aggsResponse;
			}

		}
		return $aggs;
	}

}