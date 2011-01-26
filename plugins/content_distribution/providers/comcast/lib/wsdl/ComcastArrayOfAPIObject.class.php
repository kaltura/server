<?php


class ComcastArrayOfAPIObject extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAPIObject");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


