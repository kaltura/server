<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kResponseProfileMapping
{
	/**
	 * @var string
	 */
	private $parentProperty;
	
	/**
	 * @var string
	 */
	private $filterProperty;
	
	/**
	 * @return the $parentProperty
	 */
	public function getParentProperty()
	{
		return $this->parentProperty;
	}

	/**
	 * @return the $filterProperty
	 */
	public function getFilterProperty()
	{
		return $this->filterProperty;
	}

	/**
	 * @param string $parentProperty
	 */
	public function setParentProperty($parentProperty)
	{
		$this->parentProperty = $parentProperty;
	}

	/**
	 * @param string $filterProperty
	 */
	public function setFilterProperty($filterProperty)
	{
		$this->filterProperty = $filterProperty;
	}
}