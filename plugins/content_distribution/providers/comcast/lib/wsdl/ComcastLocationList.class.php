<?php


class ComcastLocationList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:LocationList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastLocation");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


