<?php


class ComcastArrayOfJobField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfJobField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastJobField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


