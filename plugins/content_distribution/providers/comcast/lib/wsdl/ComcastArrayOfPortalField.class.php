<?php


class ComcastArrayOfPortalField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfPortalField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPortalField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


