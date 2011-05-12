<?php


class ComcastRequestList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRequest");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


