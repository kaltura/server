<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kFormatField
{
	/**
	 * @var string
	 */
	public $format;
	/**
	 * @var string
	 */
	public $typeEqual;

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function getTypeEqual()
	{
		return $this->typeEqual;
	}

	/**
	 * @param string $typeEqual
	 */
	public function setTypeEqual($typeEqual)
	{
		$this->typeEqual = $typeEqual;
	}
}
