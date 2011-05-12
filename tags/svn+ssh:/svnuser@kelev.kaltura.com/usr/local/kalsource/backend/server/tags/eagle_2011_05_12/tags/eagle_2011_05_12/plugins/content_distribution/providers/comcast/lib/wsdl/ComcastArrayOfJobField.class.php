<?php


class ComcastArrayOfJobField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastJobField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


