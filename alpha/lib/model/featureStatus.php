<?php

/**
 *
 * @package Core
 * @subpackage model
 */ 
class featureStatus
{
	/**
	 * @var featureStatusType
	 */
	protected  $statusType;
	
	/**
	 * @var int
	 */
	protected  $statusValue;
	
	public function getStatusType()
	{
		return $this->statusType;
	}
	
	public function setStatusType($v)
	{
		$this->statusType = $v;
	}
	
	public function getStatusValue()
	{
		return $this->statusValue;
	}
	
	public function setStatusValue($v)
	{
		$this->statusValue = $v;
	}
}
