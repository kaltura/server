<?php


class ComcastJobList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:JobList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastJob");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


