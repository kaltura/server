<?php


class ComcastIDSet extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


