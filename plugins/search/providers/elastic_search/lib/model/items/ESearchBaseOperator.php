<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchBaseOperator extends ESearchItem
{

	/**
	 * @var ESearchOperatorType
	 */
	protected $operator;

	/**
	 * @var array
	 */
	protected $searchItems;

	protected static $operatorTypes = array(
		ESearchOperator::ESEARCH_OPERATOR,
		ESearchNestedOperator::ESEARCH_NESTED_OPERATOR,
	);

	/**
	 * @return ESearchOperatorType
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @param ESearchOperatorType $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}

	/**
	 * @return array
	 */
	public function getSearchItems()
	{
		return $this->searchItems;
	}

	/**
	 * @param array $searchItems
	 */
	public function setSearchItems($searchItems)
	{
		$this->searchItems = $searchItems;
	}

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null)
	{
		if (!$eSearchItemsArr || !count($eSearchItemsArr))
		{
			throw new kESearchException('empty search items are not allowed', kESearchException::EMPTY_SEARCH_ITEMS_NOT_ALLOWED);
		}
		switch ($eSearchOperatorType)
		{
			case ESearchOperatorType::AND_OP:
				$boolOperator = kESearchBoolQuery::MUST_KEY;
				break;
			case ESearchOperatorType::OR_OP:
				$boolOperator = kESearchBoolQuery::SHOULD_KEY;
				break;
			case ESearchOperatorType::NOT_OP:
				$boolOperator = kESearchBoolQuery::MUST_NOT_KEY;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}

		$categorizedSearchItems = self::getCategorizedSearchItems($eSearchItemsArr);
		$outQuery = static::createSearchQueryForItems($categorizedSearchItems, $boolOperator, $queryAttributes);

		return $outQuery;
	}

	private static function getCategorizedSearchItems($eSearchItemsArr)
	{
		$categorizedSearchItems = array();
		$allCategorizedSearchItems = array();

		//categorize each different search item by type except ESearchOperator
		foreach ($eSearchItemsArr as $searchItem)
		{
			/**
			 * @var ESearchItem $searchItem
			 */
			$className = get_class($searchItem);
			if(in_array($className, self::$operatorTypes)) //ESearchOperator or ESearchNestedOperator
			{
				$allCategorizedSearchItems[] = array('className' => $className, 'items' => $searchItem, 'operatorType' => $searchItem->getOperator());
				continue;
			}

			if (!isset($categorizedSearchItems[$className]))
				$categorizedSearchItems[$className] = array();
			$categorizedSearchItems[$className][] = $searchItem;
		}

		foreach ($categorizedSearchItems as $className => $searchItems)
		{
			$allCategorizedSearchItems[] = array('className' => $className, 'items' => $searchItems);
		}

		return $allCategorizedSearchItems;
	}

	public function shouldAddLanguageSearch()
	{
		
	}

	public function getItemMappingFieldsDelimiter()
	{
		
	}

	protected static function getParamsFromCategorizedSearchItem($categorizedSearchItem)
	{
		$itemClassName = $categorizedSearchItem['className'];
		$itemSearchItems = $categorizedSearchItem['items'];
		$operatorType = null;
		if(in_array($itemClassName, self::$operatorTypes))
		{
			$itemSearchItems = $itemSearchItems->getSearchItems();
			$operatorType = $categorizedSearchItem['operatorType'];
		}

		return array($itemClassName, $itemSearchItems, $operatorType);
	}

	protected static function addSubQueryToFinalQuery(&$subQuery, &$outQuery, $itemClassName, $boolOperator)
	{
		if(in_array($itemClassName ,self::$operatorTypes))
			$outQuery->addByOperatorType($boolOperator, $subQuery);
		else
		{
			foreach ($subQuery as $key => $value)
			{
				if($boolOperator == kESearchBoolQuery::MUST_KEY && is_callable(array($value, "getShouldMoveToFilterContext")))
				{
					$moveToFilter = $value->getShouldMoveToFilterContext();
					if($moveToFilter)
					{
						$outQuery->addToFilter($value);
						continue;
					}
				}
				$outQuery->addByOperatorType($boolOperator, $value);
			}
		}
	}

}
