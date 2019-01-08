<?php

class ESearchQueryFromFilter
{
	protected $searchItems;
	protected $nestedSearchItem;

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$this->searchItems = array();
		$this->nestedSearchItem = array();
	}

	public function createElasticQueryFromFilter(baseObjectFilter $filter)
	{
		$this->init();
		foreach($filter->fields as $field => $fieldValue)
		{
			if ($field == ESearchCaptionAssetItemFilterFields::ORDER_BY || $field == ESearchCaptionAssetItemFilterFields::LIMIT)
			{
				continue;
			}

			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			list( , $operator, $fieldName) = $fieldParts;
			if(!in_array($fieldName, static::getSupportedFields()) || is_null($fieldValue) || $fieldValue === '')
			{
				continue;
			}

			$searchItemType = $this->getSphinxToElasticSearchItemType($operator);
			$elasticFieldName = $this->getSphinxToElasticFieldName($fieldName);
			if($elasticFieldName && $searchItemType)
			{
				$this->AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue);
			}
		}

		$this->addNestedQueryPart();
		$operator = new ESearchOperator();
		$operator->setOperator(ESearchOperatorType::AND_OP);
		$operator->setSearchItems($this->searchItems);
		return $operator;
	}

	public function retrieveElasticQueryEntryIds(baseObjectFilter $filter, kPager $pager)
	{
		$query = $this->createElasticQueryFromFilter($filter);

		$entrySearch = new kEntrySearch();
		$entrySearch->setFilterOnlyContext();
		$elasticResults = $entrySearch->doSearch($query, array(), null, $pager, null);

		list($coreResults, $objectOrder, $objectCount, $objectHighlight) = kESearchCoreAdapter::getElasticResultAsArray($elasticResults,
			$entrySearch->getQueryAttributes()->getQueryHighlightsAttributes());

		$entryIds = array_keys($coreResults);
		return array ($entryIds, $objectCount);
	}

	protected function AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue)
	{
		switch($searchItemType)
		{
			case ESearchFilterItemType::EXACT_MATCH:
				$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH);
				$this->addToSearchItemsByField($elasticFieldName, $searchItem);
				break;

			case ESearchFilterItemType::EXACT_MATCH_MULTI_OR :
				$this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::OR_OP);
				break;

			case ESearchFilterItemType::EXACT_MATCH_MULTI_AND :
				$this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::AND_OP);
				break;

			case ESearchFilterItemType::PARTIAL :
				$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL);
				$this->addToSearchItemsByField($elasticFieldName, $searchItem);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_OR :
				$this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL, ESearchOperatorType::OR_OP);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_AND :
				$this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL, ESearchOperatorType::AND_OP);
				break;

			case ESearchFilterItemType::RANGE_GTE :
				$this->addRangeGteQuery($elasticFieldName, $fieldValue);
				break;

			case ESearchFilterItemType::RANGE_LTE :
				$this->addRangeLteQuery($elasticFieldName, $fieldValue);
				break;

			default:
				KalturaLog::debug("Skip field [$elasticFieldName] as it has no search item type [$searchItemType]");
		}
	}

	protected function addMultiQuery($elasticFieldName, $fieldValue, $searchType, $operatorType)
	{
		$values = $this->createValuesArray($fieldValue);
		if(count($values))
		{
			$innerSearchItems = array();
			foreach ($values as $value)
			{
				$searchItem = $this->addSearchItem($elasticFieldName, $value, $searchType);
				$innerSearchItems[] = $searchItem;
			}
			$operator = $this->getEsearchOperatorByField($elasticFieldName);
			$operator->setOperator($operatorType);
			$operator->setSearchItems($innerSearchItems);
			$this->addToSearchItemsByField($elasticFieldName, $operator);
		}
	}

	protected function addRangeGteQuery($elasticFieldName, $fieldValue)
	{
		$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::RANGE, true);
		$range = new ESearchRange();
		$range->setGreaterThanOrEqual($fieldValue);
		$searchItem->setRange($range);
		$this->addToSearchItemsByField($elasticFieldName, $searchItem);
	}

	protected function addRangeLteQuery($elasticFieldName, $fieldValue)
	{
		$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::RANGE, true);
		$range = new ESearchRange();
		$range->setLessThanOrEqual($fieldValue);
		$searchItem->setRange($range);
		$this->addToSearchItemsByField($elasticFieldName, $searchItem);
	}

	protected function addSearchItem($elasticFieldName, $value, $itemType, $range = false)
	{
		$searchItem = $this->createSearchItemByFieldType($elasticFieldName);
		$searchItem->setFieldName($elasticFieldName);
		if(!$range)
		{
			$searchItem->setSearchTerm($value);
		}
		$searchItem->setItemType($itemType);
		return $searchItem;
	}

	protected function createValuesArray($val)
	{
		$values = is_array($val) ? $val : explode(',', $val);
		foreach($values as $valIndex => &$valValue)
		{
			$valValue = trim($valValue);
			if(!strlen($valValue))
			{
				unset($values[$valIndex]);
			}
		}

		return $values;
	}

	protected function addToSearchItemsByField($elasticFieldName, $searchItem)
	{
		if(in_array($elasticFieldName, static::getNestedQueryFields()))
		{
			$this->nestedSearchItem[] = $searchItem;
		}
		else
		{
			$this->searchItems[] = $searchItem;
		}
	}

	protected function getEsearchOperatorByField($elasticFieldName)
	{
		if(in_array($elasticFieldName, static::getNestedQueryFields()))
		{
			return new ESearchNestedOperator();
		}
		else
		{
			return new ESearchOperator();
		}
	}

	protected function addNestedQueryPart()
	{
		if($this->nestedSearchItem)
		{
			$nestedOperator = new ESearchNestedOperator();
			$nestedOperator->setOperator(ESearchOperatorType::AND_OP);
			$nestedOperator->setSearchItems($this->nestedSearchItem);
			$this->searchItems[] = $nestedOperator;
		}
	}

	protected function createSearchItemByFieldType($elasticFieldName)
	{
		return new ESearchEntryItem();
	}

	protected function getSphinxToElasticSearchItemType($operator)
	{
		$operatorsMap = array(
			baseObjectFilter::EQ => ESearchFilterItemType::EXACT_MATCH,
			baseObjectFilter::IN => ESearchFilterItemType::EXACT_MATCH_MULTI_OR,
			baseObjectFilter::NOT_IN => ESearchFilterItemType::EXACT_MATCH_NOT,
			baseObjectFilter::GTE => ESearchFilterItemType::RANGE_GTE,
			baseObjectFilter::LTE => ESearchFilterItemType::RANGE_LTE,
			baseObjectFilter::LIKE => ESearchFilterItemType::EXACT_MATCH,
			baseObjectFilter::MULTI_LIKE_OR => ESearchFilterItemType::EXACT_MATCH_MULTI_OR,
			baseObjectFilter::MULTI_LIKE_AND => ESearchFilterItemType::EXACT_MATCH_MULTI_AND);

		if(array_key_exists($operator, $operatorsMap))
		{
			return $operatorsMap[$operator];
		}
		else
		{
			return null;
		}
	}

	protected function getSphinxToElasticFieldName($field)
	{
			return null;
	}

	protected static function getSupportedFields()
	{
		return array();
	}

	protected static function getNestedQueryFields()
	{
		return array();
	}

}