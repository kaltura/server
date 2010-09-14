<?php
class ERROR
{
	// TODO - create some Exception that will indicate the system to go to an error page.
	static public function fatal ( $error_code , $error_str )
	{
		return;
		sfLogger::getInstance()->alert ( "Fatal error: [" . $error_code . "]: " . $error_str );	
		// throw new Exception ( "Fatal error: [" . $error_code . "]: " . $error_str );		 
	}
	
	static public function userFix ( $error_code , $error_str )
	{
		return;
		sfLogger::getInstance()->alert  ( "Fatal error: [" . $error_code . "]: " . $error_str );	
	}
}
?>