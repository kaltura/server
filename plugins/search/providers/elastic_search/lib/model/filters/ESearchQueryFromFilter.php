<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */
class ESearchQueryFromFilter
{
	protected $searchItems;

	public function createElasticQueryFromFilter(baseObjectFilter $filter)
	{
		$this->searchItems = array();
		foreach($filter->fields as $field => $fieldValue)
		{
			if ($field == ESearchCaptionAssetItemFilterFields::ORDER_BY || $field == ESearchCaptionAssetItemFilterFields::LIMIT) {
				continue;
			}

			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			list($prefix, $operator, $fieldName) = $fieldParts;
			if(in_array($fieldName, $this->getNonSupportedFields()) || is_null($fieldValue) || $fieldValue === '')
			{
				KalturaLog::debug("Skipping field [$fieldName] with value [$fieldValue]");
				continue;
			}

			$searchItemType = $this->getSphinxToElasticSearchItemType($operator);
			$elasticFieldName = $this->getSphinxToElasticFieldName($fieldName);
			if($elasticFieldName && $searchItemType)
			{
				$this->AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue);
			}
		}

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
				$this->searchItems[] = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH);
				break;

			case ESearchFilterItemType::EXACT_MATCH_MULTI_OR :
				$this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::OR_OP);
				break;

			case ESearchFilterItemType::PARTIAL :
				$this->searchItems[] = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL);
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
			foreach ($values as $value) {
				$searchItem = $this->addSearchItem($elasticFieldName, $value, $searchType);
				$innerSearchItems[] = $searchItem;
			}
			$operator = new ESearchOperator();
			$operator->setOperator($operatorType);
			$operator->setSearchItems($innerSearchItems);
			$this->searchItems[] = $operator;
		}
	}

	protected function addRangeGteQuery($elasticFieldName, $fieldValue)
	{
		$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::RANGE, true);
		$range = new ESearchRange();
		$range->setGreaterThanOrEqual($fieldValue);
		$searchItem->setRange($range);
		$this->searchItems[] = $searchItem;
	}

	protected function addRangeLteQuery($elasticFieldName, $fieldValue)
	{
		$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::RANGE, true);
		$range = new ESearchRange();
		$range->setLessThanOrEqual($fieldValue);
		$searchItem->setRange($range);
		$this->searchItems[] = $searchItem;
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

	protected function createSearchItemByFieldType($elasticFieldName)
	{
		return new ESearchEntryItem();
	}

	protected function getSphinxToElasticSearchItemType($operator)
	{
			return null;
	}

	protected function getSphinxToElasticFieldName($field)
	{
			return null;
	}

	protected function getNonSupportedFields()
	{
		return array();
	}

}