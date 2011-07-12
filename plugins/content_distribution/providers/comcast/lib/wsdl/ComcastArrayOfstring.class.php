<?php


class ComcastArrayOfstring extends SoapArray
{				
	public function getType()
	{
		return 'http://www.theplatform.com/package/java.lang/:ArrayOfstring';
	}
				
	public function __construct()
	{
		parent::__construct("string");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


