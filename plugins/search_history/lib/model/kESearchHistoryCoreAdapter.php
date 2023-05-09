<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class kESearchHistoryCoreAdapter
{

	const HITS_KEY = 'hits';
	const SOURCE_KEY = '_source';

	public static function getCoreESearchHistoryFromResults($elasticResults)
	{
		list($objects, $totalCount) = self::getCoreESearchHistoryFromHitsResults($elasticResults);
		$aggregations = self::getCoreESearchHistoryAggregationsFromResults($elasticResults);
		return array($objects, $totalCount, $aggregations);
	}

	protected static function getCoreESearchHistoryFromHitsResults($elasticResults)
	{
		$objects = array();
		$totalCount = 0;
		$duplicateMap = array();
		foreach ($elasticResults[self::HITS_KEY][self::HITS_KEY] as $key => $elasticObject)
		{
			$searchTerm = null;
			if (isset($elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCH_TERM]))
			{
				$searchTerm = $elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCH_TERM];
			}

			if (!$searchTerm || isset($duplicateMap[$searchTerm]))
				continue;

			$duplicateMap[$searchTerm] = true;
			$history = new ESearchHistory();
			$history->setSearchTerm($searchTerm);
			if (isset($elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::TIMESTAMP]))
			{
				$history->setTimestamp($elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::TIMESTAMP]);
			}
			if (isset($elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCHED_OBJECT]))
			{
				$history->setSearchedObject($elasticObject[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCHED_OBJECT]);
			}
			$objects[] = $history;
			$totalCount++;
		}
		return array($objects, $totalCount);
	}

	public static function getIdsToDeleteFromHitsResults($elasticResults)
	{
		$ids = array();
		foreach ($elasticResults[self::HITS_KEY][self::HITS_KEY] as $key => $elasticObject)
		{
			if (isset($elasticObject['_id']) && isset($elasticObject['_index']))
			{
				$result = array();
				$result['id'] = $elasticObject['_id'];
				$result['index'] = $elasticObject['_index'];
				$ids[] = $result;
			}
		}
		return $ids;
	}

	protected static function getCoreESearchHistoryAggregationsFromResults($elasticResults)
	{
		$aggregationResults = isset($elasticResults['aggregations']) ? $elasticResults['aggregations'] : array();

		$aggregations = new KalturaESearchAggregationResponseArray();
		foreach ($aggregationResults as $key => $response)
		{
			list (, $fieldName) = explode(':', $key);
			$searchHistoryAggregation = new KalturaESearchHistoryAggregationItem();
			$aggregationsResponses  = $searchHistoryAggregation->coreToApiResponse($response, $fieldName);
			foreach ($aggregationsResponses as $aggregationsResponse)
			{
				$aggregations[] = $aggregationsResponse;
			}
		}
		return $aggregations;
	}

}
