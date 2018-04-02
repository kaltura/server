<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
interface IESearchItem
{
	/**
	 * @param $eSearchItemsArr
	 * @param $boolOperator
	 * @param $queryAttributes
	 * @param null $eSearchOperatorType
	 * @return mixed
	 */
	public static function createSearchQuery($eSearchItemsArr, $boolOperator, &$queryAttributes, $eSearchOperatorType = null);
}
