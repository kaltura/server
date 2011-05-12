<?php


class ComcastArrayOfEndUserField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


