<?php


class ComcastArrayOfLocationField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfLocationField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastLocationField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


