<?php


class ComcastCategoryList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:CategoryList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCategory");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


