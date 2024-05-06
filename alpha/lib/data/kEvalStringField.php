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
		
		try {
			$retVal = eval("return @strval({$this->code});");
		} catch (TypeError $error) {
			KalturaLog::debug("Failed to evaluate code [{$this->code}] with error: " . print_r($error, true));
			return $this->getTypeErrorDefaultValue($this->code);
		}
		
		return $retVal;
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

	private function getTypeErrorDefaultValue($code)
	{
		if(kString::beginsWith($code, "unserialize"))
		{
			return false;
		}
		
		if(kString::beginsWith($code, "count"))
		{
			return 0;
		}
		
		if(kString::beginsWith($code, "explode"))
		{
			return array();
		}
		
		if(kString::beginsWith($code, "strlen"))
		{
			return 0;
		}
		
		return "";
	}
	
}