<?php


class ComcastArrayOfRoleField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfRoleField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRoleField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


