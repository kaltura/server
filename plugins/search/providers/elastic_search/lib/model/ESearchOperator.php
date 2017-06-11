<?php

class ESearchOperator extends ESearchItem
{

	/**
	 * @var ESearchOperatorType
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

	public function createSearchQuery()
	{
		return kESearchQueryManager::createOperatorSearchQuery($this);
	}

	public function createSubQuery()
	{
		return $this;
	}

	public function getType()
	{
		return 'operator';
	}


}