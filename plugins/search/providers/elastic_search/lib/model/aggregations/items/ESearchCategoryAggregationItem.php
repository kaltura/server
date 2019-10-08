<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchCategoryAggregationItem extends ESearchAggregationItem
{
	/***
	 * var ESearchCategoryAggregationFieldName
	 */
	protected $fieldName;

	const KEY = 'categories';

	public function getAggregationKey()
	{
		return self::KEY;
	}
}
