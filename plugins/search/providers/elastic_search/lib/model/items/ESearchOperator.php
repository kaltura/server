<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchOperator extends ESearchBaseOperator
{

	const ESEARCH_OPERATOR = 'ESearchOperator';

	protected static function createSearchQueryForItems($categorizedSearchItems, $boolOperator, &$queryAttributes)
	{
		$outQuery = new kESearchBoolQuery();
		foreach ($categorizedSearchItems as $categorizedSearchItem)
		{
			list($itemClassName, $itemSearchItems, $operatorType) = self::getParamsFromCategorizedSearchItem($categorizedSearchItem);

			if($itemClassName == ESearchNestedOperator::ESEARCH_NESTED_OPERATOR)
				$queryAttributes->setInitNestedQuery(true);

			$subQuery = call_user_func(array($itemClassName, 'createSearchQuery'), $itemSearchItems, $boolOperator, $queryAttributes, $operatorType);

			self::addSubQueryToFinalQuery($subQuery, $outQuery, $itemClassName, $boolOperator);
		}

		return $outQuery;
	}

}
