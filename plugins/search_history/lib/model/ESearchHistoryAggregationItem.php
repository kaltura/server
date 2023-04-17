<?php
/**
 * @package plugins.searchHistory
 * @subpackage model
 */
class ESearchHistoryAggregationItem extends ESearchAggregationItem
{
	/**
	 * @var string
	 */
	protected $fieldName;

	const KEY = 'terms';

	public  function getAggregationKey()
	{
		return self::KEY;
	}

	public function getAggregationCommand()
	{
		return array(ESearchAggregations::TERMS =>
			array(ESearchAggregations::FIELD => $this->fieldName, ESearchAggregations::SIZE => $this->getSize(),
				ESearchAggregations::EXECUTION_HINT => ESearchAggregations::MAP));
	}

}