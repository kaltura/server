<?php


class ComcastArrayOfDirectoryField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfDirectoryField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastDirectoryField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


