<?php

/**
 *
 * @package Core
 * @subpackage model
 */ 
class kFeatureStatus
{
	/**
	 * @var IndexObjectType
	 */
	protected  $type;
	
	/**
	 * @var int
	 */
	protected  $value;
	
	public function getType()
	{
		return $this->type;
	}
	
	public function settype($v)
	{
		$this->type = $v;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function setValue($v)
	{
		$this->value = $v;
	}
}
