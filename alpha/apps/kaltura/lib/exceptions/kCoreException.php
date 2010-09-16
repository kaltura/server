<?php
/**
 * @FIXME - refactor the current error codes to another exception class which will inherit from kCoreException
 */
class kCoreException extends Exception
{
	public function __construct($message, $code)
	{
		$this->message = $message;
		$this->code = $code;
	}
	
	const INVALID_QUERY = "INVALID_QUERY";
	
	const DUPLICATE_CATEGORY = "DUPLICATE_CATEGORY";
	
	const PARENT_ID_IS_CHILD = "PARENT_ID_IS_CHILD";
	
	const MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED = "MAX_NUMBER_OF_ACCESS_CONTROLS_REACHED";
	
	const MAX_NUMBER_OF_CATEGORIES_REACHED = "MAX_NUMBER_OF_CATEGORIES_REACHED";
	
	const MAX_CATEGORY_DEPTH_REACHED = "MAX_CATEGORY_DEPTH_REACHED";
	
	const MAX_CATEGORIES_PER_ENTRY = "MAX_CATEGORIES_PER_ENTRY";
}