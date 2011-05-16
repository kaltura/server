<?php


class ComcastArrayOfFormat extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfFormat';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastFormat");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


