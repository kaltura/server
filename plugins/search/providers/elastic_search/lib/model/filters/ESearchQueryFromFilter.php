<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */

class ESearchQueryFromFilter
{
	protected $searchItems;
	protected $nestedSearchItem;
	protected static $validStatuses = array(entryStatus::READY,entryStatus::NO_CONTENT);
	protected static $categoryFilterFields = array(
		ESearchBaseCategoryEntryItem::CATEGORY_NAMES_MAPPING_FIELD => array(ESearchCategoryEntryFieldName::NAME, 'ESearchCategoryEntryNameItem'),
		ESearchBaseCategoryEntryItem::CATEGORY_IDS_MAPPING_FIELD => array(ESearchCategoryEntryFieldName::ID,  'ESearchCategoryEntryIdItem'),
		ESearchCategoryEntryFieldName::ANCESTOR_ID =>  array(ESearchCategoryEntryFieldName::ANCESTOR_ID,  'ESearchCategoryEntryAncestorIdItem'),
		ESearchCategoryEntryFieldName::ANCESTOR_NAME =>  array(ESearchCategoryEntryFieldName::ANCESTOR_NAME,  'ESearchCategoryEntryAncestorNameItem')
	);

	const FIELD_NAME_LOCATION = 0;
	const FIELD_CLASS_LOCATION = 1;
	const FIELD_NAME = 'fieldName';
	const KALTURA_METADATA_SEARCH_ITEM = 'KalturaMetadataSearchItem';
	const KALTURA_CLASS = 'kalturaClass';

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$this->searchItems = array();
		$this->nestedSearchItem = array();
	}

	/**
	 * @param SimpleXMLElement $filter
	 * @return bool
	 */
	public static function canTransformXmlFilter($filter)
	{
		return (!isset($filter->advancedSearch) || (string)$filter->advancedSearch[self::KALTURA_CLASS] === self::KALTURA_METADATA_SEARCH_ITEM);
	}

	/**
	 * @param baseObjectFilter $filter
	 * @return bool
	 */
	public static function canTransformFilter($filter)
	{
		$result = true;
		foreach($filter->fields as $field => $fieldValue)
		{
			if($field === entryFilter::ORDER)
			{
				continue;
			}

			$fieldParts = explode(baseObjectFilter::FILTER_PREFIX, $field, 3);
			if (count($fieldParts) < 3)
			{
				continue;
			}

			list( , $operator, $fieldName) = $fieldParts;
			if(!in_array($fieldName, static::getSupportedFields()) && !(is_null($fieldValue) || $fieldValue == ''))
			{
				KalturaLog::debug('Cannot convert field:' . $fieldName);
				return false;
			}
		}

		if($filter->getAdvancedSearch())
		{
			$result = ESearchQueryFromAdvancedSearch::canTransformAdvanceFilter($filter->getAdvancedSearch());
		}

		return $result;
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
		return array($operator, null);
	}

	public function retrieveElasticQueryEntryIds(baseObjectFilter $filter, kPager $pager)
	{
		list($query, $kEsearchOrderBy ) = $this->createElasticQueryFromFilter($filter);

		$entrySearch = new kEntrySearch();
		$entrySearch->setFilterOnlyContext();
		$elasticResults = $entrySearch->doSearch($query, $pager, self::$validStatuses,null, $kEsearchOrderBy);

		list($coreResults, $objectOrder, $objectCount, $objectHighlight) = kESearchCoreAdapter::getElasticResultAsArray($elasticResults,
			$entrySearch->getQueryAttributes()->getQueryHighlightsAttributes());

		$entryIds = array_keys($coreResults);
		return array ($entryIds, $objectCount);
	}

	protected function AddFieldPartToQuery($searchItemType, $elasticFieldName, $fieldValue)
	{
		if(in_array($elasticFieldName, $this->getTimeFields()))
		{
			$fieldValue = kTime::getRelativeTime($fieldValue);
		}

		switch($searchItemType)
		{
			case ESearchFilterItemType::EXACT_MATCH:
				$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH);
				break;

			case ESearchFilterItemType::EXACT_MATCH_MULTI_OR :
				if ($elasticFieldName === ESearchCategoryEntryFieldName::FULL_IDS)
				{
					$searchItem = $this->addMultiQueryCategoryFullName($elasticFieldName, $fieldValue,  ESearchItemType::EXACT_MATCH, ESearchOperatorType::OR_OP);
				}
				else
				{
					$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::OR_OP);
				}

				break;

			case ESearchFilterItemType::MATCH_AND:
			case ESearchFilterItemType::EXACT_MATCH_MULTI_AND :
				$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::AND_OP);
				break;

			case ESearchFilterItemType::PARTIAL :
				$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_OR :
				$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL, ESearchOperatorType::OR_OP);
				break;

			case ESearchFilterItemType::PARTIAL_MULTI_AND :
				$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::PARTIAL, ESearchOperatorType::AND_OP);
				break;

			case ESearchFilterItemType::NOT_CONTAINS:
			case ESearchFilterItemType::EXACT_MATCH_NOT:
				$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::NOT_OP);
				break;

			case ESearchFilterItemType::IS_EMPTY:
				$searchItem = $this->addSearchItem($elasticFieldName, null, ESearchItemType::EXISTS);
				if ($fieldValue)
				{
					$searchItem = $this->createOperator(ESearchOperatorType::NOT_OP, array($searchItem), $elasticFieldName);
				}

				break;

			case ESearchFilterItemType::RANGE_GTE :
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setGreaterThanOrEqual');
				break;

			case ESearchFilterItemType::RANGE_LTE :
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setLessThanOrEqual');
				break;

			case ESearchFilterItemType::RANGE_LT:
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setLessThan');
				break;

			case ESearchFilterItemType::RANGE_GT:
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setGreaterThan');
				break;

			case ESearchFilterItemType::RANGE_LTE_OR_NULL:
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setLessThanOrEqual');
				$searchItem = $this->allowNullValues($searchItem, $elasticFieldName);
				break;

			case ESearchFilterItemType::RANGE_GTE_OR_NULL:
				$searchItem = $this->getSearchItemWithRange($elasticFieldName, $fieldValue,'setGreaterThanOrEqual');
				$searchItem = $this->allowNullValues($searchItem, $elasticFieldName);
				break;

			case ESearchFilterItemType::MATCH_OR:
				$searchItem = $this->addMultiQuery($elasticFieldName, $fieldValue, ESearchItemType::EXACT_MATCH, ESearchOperatorType::OR_OP);
				break;

			default:
				throw new KalturaAPIException(KalturaErrors::SEARCH_ITEM_TYPE_NOT_FOUND,$searchItemType, $elasticFieldName);
		}
		$this->addToSearchItemsByField($elasticFieldName, $searchItem);
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
			return $operator;
		}
	}

	protected function addSearchItem($elasticFieldName, $value, $itemType, $range = false)
	{
		$searchItem = $this->createSearchItemByFieldType($elasticFieldName);
		if (property_exists ($searchItem, self::FIELD_NAME) && !$searchItem->getFieldName())
		{
			$searchItem->setFieldName($elasticFieldName);
		}
		if(!$range)
		{
			$searchItem->setSearchTerm($value);
		}

		$searchItem->setItemType($itemType);
		return $searchItem;
	}

	protected function getSearchItemForCategories($values, $searchType)
	{
		$innerSearchItems = array();
		if(count($values))
		{
			if (count($values) > 1)
			{	//all values excepts from the last value should be parent category p1>p2>c3
				for ($i = 0; $i < count($values) - 1; $i++)
				{
					$innerSearchItems[] =  $this->addSearchItem(KalturaESearchCategoryEntryFieldName::ANCESTOR_NAME, $values[$i], $searchType);
				}
			}
			$innerSearchItems[] = $this->addSearchItem(ESearchBaseCategoryEntryItem::CATEGORY_NAMES_MAPPING_FIELD, $values[count($values) - 1], $searchType);
		}

		return $innerSearchItems;
	}

	protected function addMultiQueryCategoryFullName($elasticFieldName, $fieldValue, $searchType, $operatorType)
	{
		$categoriesValues = array();
		$andOperator = array();
		$values = $this->createValuesArray($fieldValue);
		foreach ($values as $value)
		{
			$categoriesValues[] = explode(categoryPeer::CATEGORY_SEPARATOR, $value);
		}
		foreach ($categoriesValues as $categoriesValue)
		{
			$searchItemsAndArray = $this->getSearchItemForCategories($categoriesValue, $searchType);
			$andOperator[]  = $this->createOperator(ESearchOperatorType::AND_OP, $searchItemsAndArray, $elasticFieldName);
		}

		$orOprator = $this->createOperator($operatorType, $andOperator, $elasticFieldName);
		return $orOprator;
	}

	protected function allowNullValues($searchItem,$elasticFieldName)
	{
		$notSearchItem  = $this->addSearchItem($elasticFieldName, null, ESearchItemType::EXISTS);
		$notOprator = $this->createOperator(ESearchOperatorType::NOT_OP, array($notSearchItem), $elasticFieldName);
		$orOprator = $this->createOperator(ESearchOperatorType::OR_OP, array($notOprator,$searchItem), $elasticFieldName);
		return $orOprator;
	}

	protected function createOperator($opretorType,$searchItemsArray,$elasticFieldName)
	{
		$operatorGeneral = $this->getEsearchOperatorByField($elasticFieldName);
		$operatorGeneral->setOperator($opretorType);
		$operatorGeneral->setSearchItems($searchItemsArray);
		return $operatorGeneral;
	}

	protected function getSearchItemWithRange($elasticFieldName, $fieldValue, $rangeProperty)
	{
		$searchItem = $this->addSearchItem($elasticFieldName, $fieldValue, ESearchItemType::RANGE, true);
		$range = new ESearchRange();
		$range->$rangeProperty($fieldValue);
		$searchItem->setRange($range);
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
		if (in_array($elasticFieldName ,array_keys(self::$categoryFilterFields)))
		{
			$eSearchCategoryEntry = new self::$categoryFilterFields[$elasticFieldName][self::FIELD_CLASS_LOCATION]();
			$eSearchCategoryEntry->setFieldName(self::$categoryFilterFields[$elasticFieldName][self::FIELD_NAME_LOCATION]);
			return $eSearchCategoryEntry;
		}
		if ($elasticFieldName === ESearchUnifiedItem::UNIFIED)
		{
			return new ESearchUnifiedItem();
		}

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
			baseObjectFilter::MULTI_LIKE_AND => ESearchFilterItemType::EXACT_MATCH_MULTI_AND,
			baseObjectFilter::LTE_OR_NULL => ESearchFilterItemType::RANGE_LTE_OR_NULL,
			baseObjectFilter::LT => ESearchFilterItemType::RANGE_LT,
			baseObjectFilter::GT => ESearchFilterItemType::RANGE_GT,
			baseObjectFilter::GTE_OR_NULL => ESearchFilterItemType::RANGE_GTE_OR_NULL,
			baseObjectFilter::MATCH_AND => ESearchFilterItemType::MATCH_AND,
			baseObjectFilter::MATCH_OR => ESearchFilterItemType::MATCH_OR,
			baseObjectFilter::NOT_CONTAINS => ESearchFilterItemType::NOT_CONTAINS,
			baseObjectFilter::IS_EMPTY => ESearchFilterItemType::IS_EMPTY
		);

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

	protected function getTimeFields()
	{
		return array();
	}
}
