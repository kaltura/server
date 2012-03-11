<?php

/**
 * Calculates the current time on server 
 * @package Core
 * @subpackage model.data
 */
class kTimeContextField extends kIntegerField
{
	/**
	 * Time offset in seconds since current time
	 * @var int
	 */
	protected $offset;
	
	/* (non-PHPdoc)
	 * @see kIntegerField::getFieldValue()
	 */
	protected function getFieldValue()
	{
		return time() + $this->offset;
	}
	
	/**
	 * @return int $offset
	 */
	public function getOffset() 
	{
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) 
	{
		$this->offset = $offset;
	}

	
	
}