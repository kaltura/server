<?php


class ComcastServerList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:ServerList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastServer");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


