<?php
/**
 * Effects Attribute
 *
 * @package Core
 * @subpackage model.data
 */
class kEffect
{

	/**
	 * audio fade in MilSec
	 * @var kEffectType
	 */
	private $effectType;


	/**
	 * audio fade in MilSec
	 * @var string value
	 */
	private $value;

	/**
	 * @return kEffectType
	 */
	public function getEffectType()
	{
		return $this->effectType;
	}

	/**
	 * @param kEffectType $effectType
	 */
	public function setEffectType($effectType)
	{
		$this->effectType = $effectType;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

}