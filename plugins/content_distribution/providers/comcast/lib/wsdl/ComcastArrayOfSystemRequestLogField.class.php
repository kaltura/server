<?php


class ComcastArrayOfSystemRequestLogField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfSystemRequestLogField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastSystemRequestLogField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


