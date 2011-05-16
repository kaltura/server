<?php


class ComcastArrayOfSystemStatusField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfSystemStatusField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastSystemStatusField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


