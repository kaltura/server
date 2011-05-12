<?php


class ComcastEndUserList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUser");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


