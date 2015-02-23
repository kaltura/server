<?php
/**
 * Evaluates PHP statement, depends on the execution context
 *  
 * @package Core
 * @subpackage model.data
 */
class kEvalStringField extends kStringField
{
	/**
	 * PHP code
	 * @var string
	 */
	protected $code;
	
	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null) 
	{
		if(!$scope || !$this->code)
			return null;
		
		if(strpos($this->code, ';') !== false)
			throw new kCoreException("Evaluated code may be simple value only");
			
		KalturaLog::debug("Evaluating code [$this->code]" . ($this->description ? " for description [$this->description]" : ''));
		return eval("return strval({$this->code});");
	}
	
	/**
	 * @return string $code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	
	
}