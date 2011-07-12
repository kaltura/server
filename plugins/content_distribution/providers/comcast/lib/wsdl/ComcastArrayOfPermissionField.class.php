<?php


class ComcastArrayOfPermissionField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfPermissionField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPermissionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


