<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchOperator extends ESearchItem
{

	/**
	 * @var ESearchOperatorType
	 */
	protected $operator;

	/**
	 * @var array
	 */
	protected $searchItems;

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

	public static function createSearchQuery($eSearchItemsArr, $boolOperator, $eSearchOperatorType = null)
	{
		if (!$eSearchItemsArr || !count($eSearchItemsArr))
		{
			throw new kESearchException('empty search items are not allowed', kESearchException::EMPTY_SEARCH_ITEMS_NOT_ALLOWED);
		}
		switch ($eSearchOperatorType)
		{
			case ESearchOperatorType::AND_OP:
				$boolOperator = 'must';
				break;
			case ESearchOperatorType::OR_OP:
				$boolOperator = 'should';
				break;
			case ESearchOperatorType::NOT_OP:
				$boolOperator = 'must_not';
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		
		$categorizedSearchItems = self::getCategorizedSearchItems($eSearchItemsArr);
		$outQuery = self::createSearchQueryForItems($categorizedSearchItems, $boolOperator, $eSearchOperatorType);

		return $outQuery;
	}

	private static function getCategorizedSearchItems($eSearchCaptionItemsArr)
	{
		$categorizedSearchItems = array();
		$allCategorizedSearchItems = array();

		//categorize each different search item by type except ESearchOperator
		foreach ($eSearchCaptionItemsArr as $searchItem)
		{
			/**
			 * @var ESearchItem $searchItem
			 */
			$className = get_class($searchItem);
			if($className == get_class()) //ESearchOperator
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

	private static function createSearchQueryForItems($categorizedSearchItems, $boolOperator, $eSearchOperatorType)
	{
		$outQuery = array();
		foreach ($categorizedSearchItems as $categorizedSearchItem)
		{
			$itemClassName = $categorizedSearchItem['className'];
			$itemSearchItems = $categorizedSearchItem['items'];
			$operatorType = null;
			if($itemClassName == get_class())
			{
				$itemSearchItems = $itemSearchItems->getSearchItems();
				$operatorType = $categorizedSearchItem['operatorType'];
			}
			
			$subQuery = call_user_func(array($itemClassName, 'createSearchQuery'), $itemSearchItems, $boolOperator, $operatorType);

			foreach ($subQuery as $key => $value)
			{
				if($itemClassName == get_class())
					$outQuery['bool'][$boolOperator][] = $subQuery;
				else
					$outQuery['bool'][$boolOperator][] = $value;
			}
		}

		if($eSearchOperatorType == ESearchOperatorType::OR_OP && count($outQuery['bool'][$boolOperator]))
			$outQuery['bool']['minimum_should_match'] = 1;
		
		return $outQuery;
	}

	public function getType()
	{
		return 'operator';
	}

}
