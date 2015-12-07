<?php
/**
 * @package api
 * @subpackage errors
 */
class KalturaAPIException extends Exception 
{
	protected $code;
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
		return array('code', 'message', 'args');
	}
}
