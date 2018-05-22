<?php
/**
 * @package api
 * @subpackage errors
 */
class KalturaAPIException extends Exception 
{
	protected $codeStr;
	protected $args = array ();
	
	/**
	 * @param string $errorString A string in the format: "ERR_CODE;PARAMS;MSG_STRING"
	 * @throws Exception
	 */
	function __construct( $errorString )
	{
		$errorArgs = func_get_args();
		array_shift( $errorArgs );
		$errorData = APIErrors::getErrorData( $errorString, $errorArgs );
		
		$this->message = $errorData['message'];
		$this->code = $errorData['code'];
		$this->args = $errorData['args'];
		$this->codeStr = $errorData['code'];
	}
	
	/**
	 * Get an dictionary (ARG_NAME => ARG_VALUE) of all the arguments that were passed to the exception.
	 * @return array If no args were passed the array will be empty.
	 */
	public function getArgs()
	{
		return $this->args;
	}
	
	public function __sleep()
	{
		return array('code', 'message', 'args', 'codeStr');
	}
	
	public function __wakeup()
	{
		//When running on PHP7 the code string does not get un-serialized
		//(This is probably due to the fact that the Exception base class has a code attribute which is of type int)
		if($this->codeStr)
		{
			$this->code = $this->codeStr;
		}
	}
}
