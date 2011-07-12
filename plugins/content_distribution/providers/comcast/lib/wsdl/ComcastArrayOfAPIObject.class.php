<?php


class ComcastArrayOfAPIObject extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:ArrayOfAPIObject';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastAPIObject");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


