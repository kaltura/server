<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class kESearchHistoryCoreAdapter
{

	const SUGGEST_KEY = 'suggest';
	const OPTIONS_KEY = 'options';
	const TEXT_KEY = 'text';
	const HITS_KEY = 'hits';
	const SOURCE_KEY = '_source';
	const HISTORY_SUGGEST_NAME = 'suggest_search_history';

	public static function getCoreESearchHistoryFromResults($elasticResults)
	{
		$objects = array();
		$totalCount = 0;
		if (isset($elasticResults[self::SUGGEST_KEY]))
		{
			list($objects, $totalCount) = self::getCoreESearchHistoryFromSuggestResults($elasticResults);
		}
		elseif (isset($elasticResults[self::HITS_KEY][self::HITS_KEY]))
		{
			list($objects, $totalCount) = self::getCoreESearchHistoryFromHitsResults($elasticResults);
		}
		return array($objects, $totalCount);
	}

	protected static function getCoreESearchHistoryFromSuggestResults($elasticResults)
	{
		$objects = array();
		$totalCount = 0;
		foreach ($elasticResults[self::SUGGEST_KEY] as $suggestName => $elasticObject)
		{
			if ($suggestName != self::HISTORY_SUGGEST_NAME)
			{
				continue;
			}

			foreach ($elasticObject as $key => $elasticSuggestObject)
			{
				if (isset($elasticSuggestObject[self::OPTIONS_KEY]))
				{
					self::addSuggestDataFromOptions($elasticSuggestObject, $objects, $totalCount);
				}
			}
		}
		return array($objects ,$totalCount);
	}

	protected static function addSuggestDataFromOptions($elasticSuggestObject ,&$objects, &$totalCount)
	{
		foreach ($elasticSuggestObject[self::OPTIONS_KEY] as $options)
		{
			$history = new ESearchHistory();
			if (isset($options[self::TEXT_KEY]))
			{
				$history->setSearchTerm($options[self::TEXT_KEY]);
			}
			if (isset($options[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCHED_OBJECT]))
			{
				$history->setSearchedObject($options[self::SOURCE_KEY][ESearchHistoryFieldName::SEARCHED_OBJECT]);
			}
			if (isset($options[self::SOURCE_KEY][ESearchHistoryFieldName::TIMESTAMP]))
			{
				$history->setTimestamp($options[self::SOURCE_KEY][ESearchHistoryFieldName::TIMESTAMP]);
			}
			$objects[] = $history;
			$totalCount++;
		}
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

}
