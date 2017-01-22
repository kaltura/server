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
		if(!$scope)
			return null;
			
		/* @var $scope kEventScope */
		if(strpos($this->code, ';') !== false)
			throw new kCoreException("Evaluated code may be simple value only");
		
		KalturaLog::debug("Evaluating code [$this->code]" . ($this->description ? " for description [$this->description]" : ''));
		return eval("return (bool)({$this->code});");
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