<?php
/**
 * Evaluates PHP statement, depends on the execution context
 *  
 * @package Core
 * @subpackage model.data
 */
class kEvalBooleanField extends kBooleanField
{
	/**
	 * PHP code
	 * @var bool
	 */
	protected $code;
	
	/* (non-PHPdoc)
	 * @see kBooleanField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null) 
	{
		if(strpos($this->code, ';') !== false)
			throw new kCoreException("Evaluated code may be simple value only");
			
		$val = null;
		eval("$val = (bool)({$this->code});");
		return $val;
	}
	
	/**
	 * @return bool $code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param bool $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	
	
}