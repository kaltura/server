<?php


class ComcastArrayOfRestrictionField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfRestrictionField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRestrictionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


