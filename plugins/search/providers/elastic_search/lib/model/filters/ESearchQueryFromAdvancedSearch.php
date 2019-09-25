<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */

class ESearchQueryFromAdvancedSearch
{
	const METADATA_SEARCH_FILTER = 'MetadataSearchFilter';
	const SEARCH_OPERATOR = 'AdvancedSearchFilterOperator';
	const MRP_DATA_FIELD = '/*[local-name()=\'metadata\']/*[local-name()=\'MRPData\']';

	/**
	 * @param AdvancedSearchFilterItem $advancedSearchFilterItem
	 * @return ESearchNestedOperator
	 * @throws KalturaException
	 */
	public function processAdvanceFilter($advancedSearchFilterItem)
	{
		switch(get_class($advancedSearchFilterItem))
		{
			case self::METADATA_SEARCH_FILTER:
				return $this->createESearchMetadataEntryItemsFromMetadataSearchFilter($advancedSearchFilterItem);
				break;
			case self::SEARCH_OPERATOR:
				return $this->createESearchQueryFromSearchFilterOperator($advancedSearchFilterItem);
				break;
			default:
				KalturaLog::crit('Tried to convert not supported advance filter of type:' . get_class($advancedSearchFilterItem));
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
				return ESearchOperatorType::NOT_OP;
				break;
			default:
				KalturaLog::crit('Tried to convert not supported advance filter of type:' . $type);
				throw new KalturaException();
		}
	}

	protected function createESearchQueryFromSearchFilterOperator(AdvancedSearchFilterOperator $operator)
	{
		$advanceFilterOperator = new ESearchNestedOperator();
		$advanceFilterOperator->setOperator($this->getESearchOperatorByAdvancedSearchFilterOperator($operator->getType()));
		$items = array();
		foreach($operator->getItems() as $advancedSearchFilterItem)
		{
			$items[] = $this->processAdvanceFilter($advancedSearchFilterItem);
		}

		$advanceFilterOperator->setSearchItems($items);
		return $advanceFilterOperator;
	}

	protected function getESearchItemTypeByMetadataField($field)
	{
		switch($field)
		{
			case self::MRP_DATA_FIELD:
				return ESearchItemType::STARTS_WITH;
			default:
				return ESearchItemType::EXACT_MATCH;
		}
	}

	/**
	 * @param AdvancedSearchFilterMatchCondition $filterMatchCondition
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

		if($filterMatchCondition->getNot())
		{
			$result = new ESearchNestedOperator();
			$result->setOperator(ESearchOperatorType::NOT_OP);
			$result->setSearchItems(array($item));
		}
		else
		{
			$result = $item;
		}

		return $result;
	}

	/**
	 * @param MetadataSearchFilter $searchFilter
	 * @return ESearchNestedOperator
	 * @throws KalturaException
	 */
	protected function createESearchMetadataEntryItemsFromMetadataSearchFilter(MetadataSearchFilter $searchFilter)
	{
		$advanceFilterOperator = new ESearchNestedOperator();
		$advanceFilterOperator->setOperator($this->getESearchOperatorByAdvancedSearchFilterOperator($searchFilter->getType()));
		$metadataProfileId = $searchFilter->getMetadataProfileId();
		$metaDataItems = array();
		foreach($searchFilter->getItems() as $advancedSearchFilterItem)
		{
			/* @var $advancedSearchFilterItem AdvancedSearchFilterMatchCondition */
			$metaDataItems[] = $this->createESearchMetadataItemFromFilterMatchCondition($advancedSearchFilterItem, $metadataProfileId);
		}

		$advanceFilterOperator->setSearchItems($metaDataItems);
		return $advanceFilterOperator;
	}
}