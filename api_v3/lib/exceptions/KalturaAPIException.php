<?php
/**
 * @package api
 * @subpackage errors
 */
class KalturaAPIException extends Exception 
{
	protected $code;
	private $args;
	
	/**
	 * 
	 * @param type $errorString A string in the format: "ERR_CODE;PARAMS;MSG_STRING"
	 * @throws Exception
	 */
	function KalturaAPIException($errorString)
	{
		$components = explode(';', $errorString, 3);

		if ( count($components) != 3 )
		{
			$errorString = KalturaErrors::INTERNAL_SERVERL_ERROR;
			$components = explode(';', $errorString, 3);
		}

		$this->code = $components[0];
		$this->message = $components[2];
		
		if ( ! empty($components[1]) ) // Need to process arguments?
		{
			$paramNames = explode(',', $components[1]);
			$numParamNames = count($paramNames);
			
			$funcArgs = func_get_args();
			array_shift( $funcArgs ); // Get rid of the first arg (= $errorString)
			$numArgs = count( $funcArgs );
			
			if ( $numArgs != $numParamNames ) // Test if the number of args and params match
			{
/**
 * TODO: TBD -				throw new Exception("Error: {$this->code} expected $numParamNames args but received $numArgs");
 */
			}

			// Create and fill the args dictionary
			$this->args = array();
			for ( $i = 0; $i < $numParamNames; $i++ )
			{
				// Map the arg's name to its value
				$this->args[ $paramNames[$i] ] = $funcArgs[$i];
				
				// Replace the arg's placeholder with its value in the destination string
				$this->message = str_replace("@{$paramNames[$i]}@", $funcArgs[$i], $this->message);
			}			
		}
		else
		{
			// Make sure we have an [empty] array
			$this->args = array();
		}
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
