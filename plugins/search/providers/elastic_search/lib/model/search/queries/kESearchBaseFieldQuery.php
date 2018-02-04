<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseFieldQuery extends kESearchBaseQuery
{
	const BOOST_KEY = 'boost';
	
	/**
	 * @var string
	 */
	protected $fieldName;
	
	/**
	 * @var string
	 */
	protected $boostFactor;

	/**
	 * @var bool
	 */
	protected $shouldMoveToFilterContext;

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
		return $this->shouldMoveToFilterContext;
	}

	/**
	 * @param boolean $shouldMoveToFilterContext
	 */
	public function setShouldMoveToFilterContext($shouldMoveToFilterContext)
	{
		$this->shouldMoveToFilterContext = $shouldMoveToFilterContext;
	}

}
