<?php


class ComcastUserList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:UserList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastUser");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


