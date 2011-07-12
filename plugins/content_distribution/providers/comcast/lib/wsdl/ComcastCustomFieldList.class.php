<?php


class ComcastCustomFieldList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:CustomFieldList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCustomField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


