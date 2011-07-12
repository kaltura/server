<?php


class ComcastSystemTaskList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:SystemTaskList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastSystemTask");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


