<?php


class ComcastRestrictionList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:RestrictionList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRestriction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


