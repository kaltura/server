<?php


class ComcastArrayOfSystemTaskField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfSystemTaskField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastSystemTaskField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


