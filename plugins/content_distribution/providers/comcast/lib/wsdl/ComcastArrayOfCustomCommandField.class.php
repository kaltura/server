<?php


class ComcastArrayOfCustomCommandField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfCustomCommandField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommandField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


