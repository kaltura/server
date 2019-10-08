<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class ESearchEntryAggregationItem extends ESearchAggregationItem
{
	/**
	 * var ESearchEntryAggregationFieldName
	 */
	protected $fieldName;

	const KEY = 'entries';

	public  function getAggregationKey()
	{
		return self::KEY;
	}

}