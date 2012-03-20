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
	 * @return int|string
	 */
	abstract public function getValue();

	/**
	 * @param int|string $value
	 */
	abstract public function setValue($value);
}