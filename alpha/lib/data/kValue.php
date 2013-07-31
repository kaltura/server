<?php

/**
 * Base abstraction for any value, constant or calculated that retreived from the API 
 * @package Core
 * @subpackage model.data
 */
abstract class kValue
{
	/**
	 * @var int|string|bool
	 */
	protected $value;
	
	/**
	 * @var string
	 */
    protected $description;
	
	/**
	 * @return int|string
	 */
	abstract public function getValue();

	/**
	 * @param int|string $value
	 */
	abstract public function setValue($value);
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
}