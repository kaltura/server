<?php


class ComcastArrayOfAccountField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfAccountField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastAccountField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


