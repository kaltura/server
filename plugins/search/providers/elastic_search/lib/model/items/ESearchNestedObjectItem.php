<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchNestedObjectItem extends ESearchItem
{

	const DEFAULT_INNER_HITS_SIZE = 10;
	const DEFAULT_GROUP_NAME = 'default_group';
	const QUERY_NAME_DELIMITER = '#DEL#';
	const SUBTYPE_DELIMITER = '#SUBTYPE_DEL#';

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

	protected static function initializeNumOfFragments()
	{
		$highlightConfigKey = static::HIGHLIGHT_CONFIG_KEY;
		$numOfFragments = elasticSearchUtils::getNumOfFragmentsByConfigKey($highlightConfigKey);
		return $numOfFragments;
	}

	protected static function createNestedQueryForItems($eSearchItemsArr, $boolOperator, &$queryAttributes)
	{
		$innerHitsSize = self::initializeInnerHitsSize($queryAttributes);
		$allowedSearchTypes = static::getAllowedSearchTypesForField();

		// must_not was already set in a higher level of the query inside ESearchOperator
		if($boolOperator == 'must_not')
			$boolOperator = 'must';

		//don't group to a single query if the operator is AND
		if($boolOperator == 'must')
			$finalQuery = static::createNestedQueries($eSearchItemsArr, $innerHitsSize,$queryAttributes,$boolOperator,$allowedSearchTypes);
		else
			$finalQuery = static::createGroupedNestedQueries($eSearchItemsArr, $innerHitsSize, $queryAttributes, $boolOperator, $allowedSearchTypes);

		return $finalQuery;
	}

	protected static function createNestedQueries($eSearchItemsArr, $innerHitsSize, &$queryAttributes, $boolOperator, $allowedSearchTypes)
	{
		$finalQuery = array();
		foreach ($eSearchItemsArr as $eSearchItem)
		{
			$queryNames = $eSearchItem->getNestedQueryNames();
			$bool = new kESearchBoolQuery();
			foreach ($queryNames as $nestedQueryName)
			{
				$subType = self::getSubTypeFromQueryName($nestedQueryName);
				if($subType)
					$queryAttributes->setObjectSubType($subType);

				$innerNestedQuery = self::createSingleNestedQuery($innerHitsSize, $queryAttributes, $boolOperator, $allowedSearchTypes, $eSearchItem);
				$innerNestedQuery->setInnerHitsName($nestedQueryName);
				$queryAttributes->setObjectSubType(null);
				$bool->addToShould($innerNestedQuery);
			}
			$nestedQuery = $bool;
			$finalQuery[] = $nestedQuery;
		}
		return $finalQuery;
	}

	/**
	 * @param $innerHitsSize
	 * @param $queryAttributes
	 * @param $boolOperator
	 * @param $allowedSearchTypes
	 * @param $eSearchItem
	 * @return array
	 */
	protected static function createSingleNestedQuery($innerHitsSize, &$queryAttributes, $boolOperator, $allowedSearchTypes, $eSearchItem)
	{
		$nestedQuery = new kESearchNestedQuery();
		$nestedQuery->setPath(static::NESTED_QUERY_PATH);
		$nestedQuery->setInnerHitsSize($innerHitsSize);
		$nestedQuery->setInnerHitsSource(true);
		$queryAttributes->setScopeToInner();
		$boolQuery = new kESearchBoolQuery();
		static::createSingleItemSearchQuery($eSearchItem, $boolOperator, $boolQuery, $allowedSearchTypes, $queryAttributes);
		$numOfFragments = self::initializeNumOfFragments();
		$highlight = new kESearchHighlightQuery($queryAttributes->getFieldsToHighlight(), $numOfFragments);
		$nestedQuery->setHighlight($highlight->getFinalQuery());
		$nestedQuery->setQuery($boolQuery);
		return $nestedQuery;
	}

	protected static function createGroupedNestedQueries($eSearchItemsArr, $innerHitsSize, &$queryAttributes, $boolOperator, $allowedSearchTypes, $name = null)
	{
		$finalQuery = array();
		$groupedItems = self::groupItemsByQueryName($eSearchItemsArr);

		foreach ($groupedItems as $name => $items)
		{
			$nestedQuery = self::createGroupedNestedQuery($items, $innerHitsSize, $queryAttributes, $boolOperator, $allowedSearchTypes, $name);
			$finalQuery[] = $nestedQuery[0];
		}
		return $finalQuery;
	}

	protected static function createGroupedNestedQuery($eSearchItemsArr, $innerHitsSize, &$queryAttributes, $boolOperator, $allowedSearchTypes, $groupQueryName = null)
	{
		$finalQuery = array();
		$nestedQuery = new kESearchNestedQuery();
		$nestedQuery->setPath(static::NESTED_QUERY_PATH);
		$nestedQuery->setInnerHitsSize($innerHitsSize);
		$nestedQuery->setInnerHitsSource(true);
		if($groupQueryName)
			$nestedQuery->setInnerHitsName($groupQueryName);
		$queryAttributes->setScopeToInner();
		$boolQuery = new kESearchBoolQuery();
		$subType = self::getSubTypeFromQueryName($groupQueryName);
		if($subType)
			$queryAttributes->setObjectSubType($subType);
		foreach ($eSearchItemsArr as $eSearchItem)
		{
			static::createSingleItemSearchQuery($eSearchItem, $boolOperator, $boolQuery, $allowedSearchTypes, $queryAttributes);
		}
		$queryAttributes->setObjectSubType(null);
		$highlight = kBaseSearch::getHighlightSection(static::HIGHLIGHT_CONFIG_KEY, $queryAttributes);
		if(isset($highlight))
			$nestedQuery->setHighlight($highlight);

		$numOfFragments = self::initializeNumOfFragments();
		$highlight = new kESearchHighlightQuery($queryAttributes->getFieldsToHighlight(), $numOfFragments);
		$nestedQuery->setHighlight($highlight->getFinalQuery());

		$nestedQuery->setQuery($boolQuery);
		$finalQuery[] = $nestedQuery;

		return $finalQuery;
	}

	public abstract function getNestedQueryNames();

	protected static function groupItemsByQueryName($eSearchItemsArr)
	{
		$groupedItems = array();
		foreach ($eSearchItemsArr as $item)
		{
			$nestedQueryNames = $item->getNestedQueryNames();
			if (!empty($nestedQueryNames))
			{
				foreach ($nestedQueryNames as $nestedQueryName)
					$groupedItems[$nestedQueryName][] = $item;
			} else
				$groupedItems[self::DEFAULT_GROUP_NAME][] = $item;
		}
		return $groupedItems;
	}

	protected static function getSubTypeFromQueryName($queryName)
	{
		$subType = null;
		$subTypeDelimiterIndex = strpos($queryName, self::SUBTYPE_DELIMITER);
		if ($subTypeDelimiterIndex !== false)
			$subType = substr($queryName, $subTypeDelimiterIndex + strlen(self::SUBTYPE_DELIMITER));

		return $subType;
	}

}
