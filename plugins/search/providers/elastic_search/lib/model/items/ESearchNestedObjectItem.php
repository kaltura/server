<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchNestedObjectItem extends ESearchItem
{

	const DEFAULT_INNER_HITS_SIZE = 10;

	protected static function initializeInnerHitsSize($queryAttributes)
	{
		$overrideInnerHitsSize = $queryAttributes->getOverrideInnerHitsSize();
		if($overrideInnerHitsSize)
			return $overrideInnerHitsSize;

		$innerHitsConfig = kConf::get('innerHits', 'elastic');
		$innerHitsConfigKey = static::INNER_HITS_CONFIG_KEY;
		$innerHitsSize = isset($innerHitsConfig[$innerHitsConfigKey]) ? $innerHitsConfig[$innerHitsConfigKey] : self::DEFAULT_INNER_HITS_SIZE;

		return $innerHitsSize;
	}

	protected static function createNestedQueryForItems($eSearchItemsArr, $boolOperator, &$queryAttributes)
	{
		$finalQuery = array();
		$innerHitsSize = self::initializeInnerHitsSize($queryAttributes);
		$allowedSearchTypes = static::getAllowedSearchTypesForField();
		
		//don't group to a single query if the operator is AND
		if($boolOperator == 'must')
		{
			foreach ($eSearchItemsArr as $eSearchItem)
			{
				$nestedQuery = null;
				$nestedQuery['nested']['path'] = static::NESTED_QUERY_PATH;
				$nestedQuery['nested']['inner_hits'] = array('size' => $innerHitsSize, '_source' => true);
				$queryAttributes->setScopeToInner();
				static::createSingleItemSearchQuery($eSearchItem, $boolOperator, $nestedQuery, $allowedSearchTypes, $queryAttributes);
				$highlight = kBaseSearch::getHighlightSection(static::HIGHLIGHT_CONFIG_KEY, $queryAttributes);
				if(isset($highlight))
					$nestedQuery['nested']['inner_hits']['highlight'] = $highlight;
				$finalQuery[] = $nestedQuery;
			}
		}
		else
		{
			$nestedQuery['nested']['path'] = static::NESTED_QUERY_PATH;;
			$nestedQuery['nested']['inner_hits'] = array('size' => $innerHitsSize, '_source' => true);
			$queryAttributes->setScopeToInner();
			foreach ($eSearchItemsArr as $eSearchItem)
			{
				static::createSingleItemSearchQuery($eSearchItem, $boolOperator, $nestedQuery, $allowedSearchTypes, $queryAttributes);
			}
			$highlight = kBaseSearch::getHighlightSection(static::HIGHLIGHT_CONFIG_KEY, $queryAttributes);
			if(isset($highlight))
				$nestedQuery['nested']['inner_hits']['highlight'] = $highlight;
			$finalQuery[] = $nestedQuery;
		}
		return $finalQuery;
	}

}
