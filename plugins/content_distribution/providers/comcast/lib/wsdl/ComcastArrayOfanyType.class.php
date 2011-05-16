<?php


class ComcastArrayOfanyType extends SoapArray
{				
	public function getType()
	{
		return 'http://www.theplatform.com/package/java.lang/:ArrayOfanyType';
	}
				
	public function __construct()
	{
		parent::__construct("anyType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


