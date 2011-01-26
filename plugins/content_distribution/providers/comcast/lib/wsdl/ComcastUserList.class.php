<?php


class ComcastUserList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUser");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


