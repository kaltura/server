<?php

class UltraSearchOperator extends UltraSearchItem
{

	/**
	 * @var UltraSearchOperatorType
	 */
	protected $operator;

	/**
	 * @var boolean
	 */
	protected $not;

	/**
	 * @var array
	 */
	protected $searchItems;

	/**
	 * @return UltraSearchOperatorType
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @param UltraSearchOperatorType $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}

	/**
	 * @return boolean
	 */
	public function isNot()
	{
		return $this->not;
	}

	/**
	 * @param boolean $not
	 */
	public function setNot($not)
	{
		$this->not = $not;
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

	public function getSearchQuery()
	{
		if (!count($this->getSearchItems()))
		{
			return array();
		}
		$boolOpeartor = null;
		$additionalParams = array();
		switch ($this->getOperator())
		{
			case UltraSearchOperatorType::AND_OP:
				$boolOpeartor = 'must';
				break;
			case UltraSearchOperatorType::OR_OP:
				$boolOpeartor = 'should';
				$additionalParams['minimum_should_match'] = 1;
				break;
			default:
				KalturaLog::crit('unknown operator type');
				return null;
		}
		$outQuery = array();
		foreach ($this->getSearchItems() as $searchItem)
		{
			/**
			 * @var UltraSearchItem $searchItem
			 */
			$outQuery[$boolOpeartor] = $searchItem->getSearchQuery();
			foreach ($additionalParams as $addParamKey => $addParamVal)
			{
				$outQuery[$addParamKey] = $addParamVal;
			}
		}

		return $outQuery;
	}


}