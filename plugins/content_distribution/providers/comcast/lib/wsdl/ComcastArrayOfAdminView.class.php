<?php


class ComcastArrayOfAdminView extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:ArrayOfAdminView';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastAdminView");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


