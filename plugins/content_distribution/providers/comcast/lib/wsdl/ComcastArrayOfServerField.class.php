<?php


class ComcastArrayOfServerField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfServerField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastServerField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


