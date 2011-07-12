<?php


class ComcastArrayOfUserField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfUserField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastUserField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


