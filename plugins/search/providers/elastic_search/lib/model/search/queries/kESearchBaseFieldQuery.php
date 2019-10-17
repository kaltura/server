<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseFieldQuery extends kESearchBaseQuery
{
	const BOOST_KEY = 'boost';
	const OPERATOR = 'operator';
	const OP_AND = 'and';
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
	 * @var boolean
	 */
	public $allWordsMustAppear = false;

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
	 * @param boolean
	 */
	public function setAllWordsMustAppear($allWordsMustAppear)
	{
		$this->allWordsMustAppear = $allWordsMustAppear;
	}

	/**
	 * @return boolean
	 */
	public function getAllWordsMustAppear()
	{
		return $this->allWordsMustAppear;
	}

}
