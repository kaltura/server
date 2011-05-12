<?php


class ComcastArrayOfUserField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUserField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


