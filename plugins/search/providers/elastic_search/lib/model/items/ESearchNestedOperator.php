<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchNestedOperator extends ESearchBaseOperator
{

	const ESEARCH_NESTED_OPERATOR = 'ESearchNestedOperator';

	protected static function createSearchQueryForItems($categorizedSearchItems, $boolOperator, &$queryAttributes)
	{
		$shouldCreateNested = false;
		if($queryAttributes->isInitNestedQuery())
		{
			$shouldCreateNested = true;
			$queryAttributes->setNestedOperatorContext(true);
			$queryAttributes->setInitNestedQuery(false);
			$queryAttributes->setScopeToInner();
			$queryAttributes->resetNestedOperatorObjectTypes();
		}

		$outQuery = new kESearchBoolQuery();
		foreach ($categorizedSearchItems as $categorizedSearchItem)
		{
			list($itemClassName, $itemSearchItems, $operatorType) = self::getParamsFromCategorizedSearchItem($categorizedSearchItem);
			$queryAttributes->addToNestedOperatorObjectTypes($itemClassName);
			$subQuery = call_user_func(array($itemClassName, 'createSearchQuery'), $itemSearchItems, $boolOperator, $queryAttributes, $operatorType);
			self::addSubQueryToFinalQuery($subQuery, $outQuery, $itemClassName, $boolOperator);
		}

		if($shouldCreateNested)
		{
			if(!$queryAttributes->validateNestedOperatorObjectTypes())
				throw new kESearchException('mixed search items in nested operator not allowed', kESearchException::MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED);

			$outQuery = kESearchQueryManager::getNestedQuery($outQuery, $queryAttributes);
			$queryAttributes->setNestedQueryName(null);
			$queryAttributes->setNestedOperatorContext(false);
		}

		return $outQuery;
	}

}
