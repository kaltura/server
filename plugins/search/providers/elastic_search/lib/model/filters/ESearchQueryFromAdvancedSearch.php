<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */
class ESearchQueryFromAdvancedSearch
{
	const METADATA_SEARCH_FILTER = 'MetadataSearchFilter';
	const SEARCH_OPERATOR = 'AdvancedSearchFilterOperator';
	const ADVANCED_SEARCH_FILTER_MATCH_CONDITION = 'AdvancedSearchFilterMatchCondition';
	const ADVANCED_SEARCH_FILTER_CONDITION = 'AdvancedSearchFilterCondition';
	const ENTRY_CAPTION_ADVANCED_FILTER = 'kEntryCaptionAdvancedFilter';
	const QUIZ_ADVANCED_FILTER = 'kQuizAdvancedFilter';
	const MRP_DATA_FIELD = '/*[local-name()=\'metadata\']/*[local-name()=\'MRPData\']';

	/**
	 * @param AdvancedSearchFilterItem $advancedSearchFilterItem
	 * @return ESearchOperator
	 * @throws kCoreException
	 */
	public function processAdvanceFilter($advancedSearchFilterItem)
	{
		if(!$advancedSearchFilterItem)
		{
			return null;
		}

		switch(get_class($advancedSearchFilterItem))
		{
			case self::METADATA_SEARCH_FILTER:
				return $this->createESearchMetadataEntryItemsFromMetadataSearchFilter($advancedSearchFilterItem);
				break;
			case self::SEARCH_OPERATOR:
				return $this->createESearchQueryFromSearchFilterOperator($advancedSearchFilterItem);
				break;
			case self::ENTRY_CAPTION_ADVANCED_FILTER:
				return $this->createESearchQueryFromEntryCaptionAdvancedFilter($advancedSearchFilterItem);
			case self::QUIZ_ADVANCED_FILTER:
				return $this->createESearchQueryFromEntryQuizAdvancedFilter($advancedSearchFilterItem);
			default:
				KalturaLog::crit('Tried to convert not supported advance filter of type:' . get_class($advancedSearchFilterItem));
				return null;
		}
	}

	protected function getESearchOperatorByAdvancedSearchFilterOperator($type)
	{
		switch($type)
		{
			case MetadataSearchFilter::SEARCH_AND:
				return ESearchOperatorType::AND_OP;
				break;
			case MetadataSearchFilter::SEARCH_OR:
				return ESearchOperatorType::OR_OP;
				break;
			default:
				KalturaLog::crit('Tried to convert not supported advance filter of type:' . $type);
				throw new kCoreException(kESearchException::MISSING_OPERATOR_TYPE);
		}
	}

	protected function createESearchQueryFromSearchFilterOperator(AdvancedSearchFilterOperator $operator)
	{
		$advanceFilterOperator = new ESearchOperator();
		$advanceFilterOperator->setOperator($this->getESearchOperatorByAdvancedSearchFilterOperator($operator->getType()));
		$items = array();
		if(!$operator->getItems())
		{
			return null;
		}

		foreach($operator->getItems() as $advancedSearchFilterItem)
		{
			$item = $this->processAdvanceFilter($advancedSearchFilterItem);
			if($item)
			{
				$items[] = $item;
			}
		}
		if (!$items)
		{
			return null;
		}

		$advanceFilterOperator->setSearchItems($items);
		return $advanceFilterOperator;
	}

	protected function createESearchQueryFromEntryCaptionAdvancedFilter(kEntryCaptionAdvancedFilter $searchFilter)
	{
		$item = new ESearchCaptionItem();
		$item->setFieldName(ESearchCaptionFieldName::CONTENT);
		$item->setItemType(ESearchItemType::EXISTS);
		if($searchFilter->getHasCaption())
		{
			$result = $item;
		}
		else
		{
			$result = self::createNegativeQuery($item);
		}

		return $result;
	}

	protected function createESearchQueryFromEntryQuizAdvancedFilter(kQuizAdvancedFilter $filter)
	{
		$item = new ESearchEntryItem();
		$item->setFieldName(ESearchEntryFieldName::IS_QUIZ);
		$item->setItemType(ESearchItemType::EXISTS);
		if($filter->getIsQuiz())
		{
			$result = $item;
		}
		else
		{
			$result = self::createNegativeQuery($item);
		}

		return $result;
	}

	protected static function createNegativeQuery($item)
	{
		$result = new ESearchOperator();
		$result->setOperator(ESearchOperatorType::NOT_OP);
		$result->setSearchItems(array($item));
		return $result;
	}

	/**
	 * Some fields have special usage in the sphinx so we need to return the relevant ESearchItemType for it
	 * @param $field
	 * @return int
	 */
	protected function getESearchItemTypeByMetadataField($field)
	{
		switch($field)
		{
			case self::MRP_DATA_FIELD:
				/**
				 * MRPData use , in the value but since its not defined as a legal character the sphinx split the value in there which make data like
				 * ><MRPData>7391,4,18086</MRPData> returns when we query with just for 7391,4
				 */
				return ESearchItemType::STARTS_WITH;
			default:
				return ESearchItemType::EXACT_MATCH;
		}
	}

	/**
	 * @param AdvancedSearchFilterCondition $filterMatchCondition
	 * @param $metadataProfileId
	 * @return ESearchItem
	 */
	protected function createESearchMetadataItemFromFilterMatchCondition($filterMatchCondition, $metadataProfileId)
	{
		$item = new ESearchMetadataItem();
		$item->setSearchTerm($filterMatchCondition->getValue());
		$item->setItemType($this->getESearchItemTypeByMetadataField($filterMatchCondition->getField()));
		$item->setXpath($filterMatchCondition->getField());
		$item->setMetadataProfileId($metadataProfileId);
		if($filterMatchCondition instanceof AdvancedSearchFilterMatchCondition && $filterMatchCondition->getNot())
		{
			$result = self::createNegativeQuery($item);
		}
		else
		{
			$result = $item;
		}

		return $result;
	}

	/**
	 * @param MetadataSearchFilter $searchFilter
	 * @return ESearchOperator
	 * @throws kCoreException
	 */
	protected function createESearchMetadataEntryItemsFromMetadataSearchFilter(MetadataSearchFilter $searchFilter)
	{
		$advanceFilterOperator = new ESearchOperator();
		$advanceFilterOperator->setOperator($this->getESearchOperatorByAdvancedSearchFilterOperator($searchFilter->getType()));
		$metadataProfileId = $searchFilter->getMetadataProfileId();
		$metaDataItems = array();
		if(!$searchFilter->getItems())
		{
			return null;
		}

		foreach($searchFilter->getItems() as $advancedSearchFilterItem)
		{
			$metaDataItems[] = $this->createESearchMetadataItemFromFilterMatchCondition($advancedSearchFilterItem, $metadataProfileId);
		}

		$advanceFilterOperator->setSearchItems($metaDataItems);
		return $advanceFilterOperator;
	}

	public static function canTransformAdvanceFilter($item)
	{
		$type = get_class($item);
		$result = self::canTransformType($type, $item);
		if($result && $item instanceof AdvancedSearchFilterOperator && is_array($item->getItems()))
		{
			foreach($item->getItems() as $item)
			{
				$result = self::canTransformAdvanceFilter($item);
				if(!$result)
				{
					return false;
				}
			}
		}

		return $result;
	}

	protected static function canTransformType($type, $item)
	{
		switch($type)
		{
			case self::SEARCH_OPERATOR:
			case self::METADATA_SEARCH_FILTER:
				return self::gotESearchOperator($item->getType());
			case self::ADVANCED_SEARCH_FILTER_MATCH_CONDITION:
			case self::ADVANCED_SEARCH_FILTER_CONDITION:
			case self::ENTRY_CAPTION_ADVANCED_FILTER:
			case self::QUIZ_ADVANCED_FILTER:
				return true;
			default:
				return false;
		}
	}

	protected static function gotESearchOperator($type)
	{
		switch($type)
		{
			case KalturaSearchOperatorType::SEARCH_AND:
			case KalturaSearchOperatorType::SEARCH_OR:
				return true;
			default:
				return false;
		}
	}

}