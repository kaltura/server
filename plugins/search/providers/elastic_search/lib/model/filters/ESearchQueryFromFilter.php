<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */
class ESearchQueryFromFilter
{
	protected $searchItems;

	/**
	 *
	 * @param baseObjectFilter $filter
	 */
	public function runElasticQueryFromFilter(baseObjectFilter $filter, kPager $pager)
	{
		$this->searchItems = array();
		foreach($filter->fields as $field => $fieldValue)
		{
			//add relevant handeling
			if ($field == '_order_by' || $field == '_limit') {
				continue;
			}

			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			list($prefix, $operator, $fieldName) = $fieldParts;
			if(in_array($fieldName, $this->getNonSupportedFields()) || is_null($fieldValue) || $fieldValue === '')
			{
				continue;
			}

			$searchItemType = $this->getSphinxToElasticSearchItemType($operator);
			$elasticFieldName = $this->getSphinxToElasticFieldName($fieldName);
			if(!$elasticFieldName || !$searchItemType)
			{
				continue;
			}
			$this->AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue);
		}

		KalturaLog::debug(print_r($this->searchItems, true));
		$operator = new ESearchOperator();
		$operator->setOperator(ESearchOperatorType::AND_OP);
		$operator->setSearchItems($this->searchItems);
		$entrySearch = new kEntrySearch();

		$elasticResults = $entrySearch->doSearch($operator, array(),null, $pager , null);

		list($coreResults, $objectCount) = kESearchCoreAdapter::getElasticResultAsArray($elasticResults,
			$entrySearch->getQueryAttributes()->getQueryHighlightsAttributes());

	}

	protected function AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue)
	{
		switch($searchItemType)
		{
			case ESearchFilterItemType::EXACT_MATCH:
				$this->searchItems[] = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH);
				break;

			case ESearchFilterItemType::EXACT_MATCH_MULTI :
				$this->addExactMatchMultiQuery($elasticFieldName, $fieldValue);
				break;

			case ESearchFilterItemType::PARTIAL :
				$this->searchItems[] = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_OR :
				$this->addPartialMultiQuery($elasticFieldName, $fieldValue, ESearchOperatorType::OR_OP);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_AND :
				$this->addPartialMultiQuery($elasticFieldName, $fieldValue, ESearchOperatorType::AND_OP);
				break;

			case ESearchFilterItemType::RANGE_GTE :
				$this->addRangeGteQuery($elasticFieldName, $fieldValue);
				break;

			case ESearchFilterItemType::RANGE_LTE :
				$this->addRangeLteQuery($elasticFieldName, $fieldValue);
				break;

			default:
				KalturaLog::debug("Skip field [$elasticFieldName] has no search item type [$searchItemType]");
		}
	}

	protected function addExactMatchMultiQuery($elasticFieldName, $fieldValue)
	{
		$values = $this->createValuesArray($fieldValue);
		if(count($values))
		{
			foreach ($values as $value) {
				$this->searchItems[] = $this->addSearchItem($elasticFieldName, $value, ESearchItemType::EXACT_MATCH);
			}
		}
	}

	protected function addPartialMultiQuery($elasticFieldName, $fieldValue, $operatorType)
	{
		$values = $this->createValuesArray($fieldValue);
		if(count($values))
		{
			$innerSearchItems = array();
			foreach ($values as $value) {
				$searchItem = $this->addSearchItem($elasticFieldName, $value, ESearchItemType::PARTIAL);
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