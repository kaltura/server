<?php


class ComcastArrayOfQuery extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:ArrayOfQuery';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastQuery");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


