<?php


class ComcastArrayOfEndUserField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfEndUserField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUserField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


