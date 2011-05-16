<?php


class ComcastIDSet extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:IDSet';
	}
				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


