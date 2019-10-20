<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseFieldQuery extends kESearchBaseQuery
{
	const BOOST_KEY = 'boost';
	const OPERATOR = 'operator';
	const CUTOFF_FREQUENCY = 'cutoff_frequency';
	
	/**
	 * @var string
	 */
	protected $fieldName;
	
	/**
	 * @var string
	 */
	protected $boostFactor;

	/**
	 * @var string
	 */
	protected $operator;

	/**
	 * @var float
	 */
	protected $cutOffFreq;

	/**
	 * @return string
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}
	
	/**
	 * @param string $fieldName
	 */
	public function setFieldName($fieldName)
	{
		$this->fieldName = $fieldName;
	}
	
	/**
	 * @return string
	 */
	public function getBoostFactor()
	{
		return $this->boostFactor;
	}
	
	/**
	 * @param string $boostFactor
	 */
	public function setBoostFactor($boostFactor)
	{
		$this->boostFactor = $boostFactor;
	}

	/**
	 * @return boolean
	 */
	public function getShouldMoveToFilterContext()
	{
		if($this->getBoostFactor() == kESearchQueryManager::DEFAULT_BOOST_FACTOR)
			return true;
		return false;
	}


	/**
	 * @param string
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}

	/**
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @param float
	 */
	public function setCutOffFreq($cutOffFreq)
	{
		$this->cutOffFreq = $cutOffFreq;
	}

	/**
	 * @return float
	 */
	public function getCutOffFreq()
	{
		return $this->cutOffFreq;
	}



}
