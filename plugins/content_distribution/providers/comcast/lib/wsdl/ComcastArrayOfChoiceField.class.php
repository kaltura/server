<?php


class ComcastArrayOfChoiceField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastChoiceField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


