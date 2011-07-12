<?php


class ComcastArrayOfCustomFieldField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfCustomFieldField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCustomFieldField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


