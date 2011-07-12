<?php


class ComcastArrayOfCategoryField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfCategoryField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCategoryField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


