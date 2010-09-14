<?php
class KalturaAPIException extends Exception 
{
	protected $code;
	
	
	public function KalturaAPIException($errorString)
	{
		$pos = strpos($errorString, ",");
		if ($pos === false)
		{
			$errorString = KalturaErrors::INTERNAL_SERVERL_ERROR;
			$pos = strpos($errorString, ",");
		}
		$this->code = substr($errorString, 0, $pos);
		$message = substr($errorString, $pos + 1);
		
		$args = func_get_args();
		array_shift($args);
		$this->message = @call_user_func_array('sprintf', array_merge(array($message), $args)); 
	}
}
