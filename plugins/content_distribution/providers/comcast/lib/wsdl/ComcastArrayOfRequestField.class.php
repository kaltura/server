<?php


class ComcastArrayOfRequestField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRequestField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


