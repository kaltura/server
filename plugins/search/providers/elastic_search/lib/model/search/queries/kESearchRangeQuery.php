<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchRangeQuery extends kESearchBaseFieldQuery
{
	const RANGE_KEY = 'range';
	const GT_KEY = 'gt';
	const GTE_KEY = 'gte';
	const LT_KEY = 'lt';
	const LTE_KEY = 'lte';

	/**
	 * @var ESearchRange $rangeObject
	 */
	protected $rangeObject;

	public function __construct(ESearchRange $rangeObject, $fieldName)
	{
		$this->rangeObject = $rangeObject;
		$this->fieldName = $fieldName;
	}


	public function getFinalQuery()
	{
		$rangeSubQuery = array();
		if(is_numeric($this->rangeObject->getGreaterThan()))
			$rangeSubQuery[self::GT_KEY] = $this->rangeObject->getGreaterThan();
		if(is_numeric($this->rangeObject->getGreaterThanOrEqual()))
			$rangeSubQuery[self::GTE_KEY] = $this->rangeObject->getGreaterThanOrEqual();
		if(is_numeric($this->rangeObject->getLessThan()))
			$rangeSubQuery[self::LT_KEY] = $this->rangeObject->getLessThan();
		if(is_numeric($this->rangeObject->getLessThanOrEqual()))
			$rangeSubQuery[self::LTE_KEY] = $this->rangeObject->getLessThanOrEqual();

		$query[self::RANGE_KEY][$this->fieldName] = $rangeSubQuery;

		if($this->getBoostFactor())
			$query[self::RANGE_KEY][$this->fieldName][self::BOOST_KEY] = $this->getBoostFactor();

		return $query;
	}
}
