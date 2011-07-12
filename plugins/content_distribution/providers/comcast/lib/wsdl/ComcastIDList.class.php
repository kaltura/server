<?php


class ComcastIDList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:IDList';
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


