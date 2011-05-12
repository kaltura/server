<?php


class ComcastServerList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastServer");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


