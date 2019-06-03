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

	public function getAggregationCommand()
	{
		return array('terms' =>
				array('field' => $this->fieldName,
						'size' =>$this->getSize()));
	}
	public  function getAggregationKey()
	{
		return self::KEY;
	}

}