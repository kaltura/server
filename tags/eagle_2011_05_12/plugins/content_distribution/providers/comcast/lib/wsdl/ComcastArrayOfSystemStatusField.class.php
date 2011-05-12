<?php


class ComcastArrayOfSystemStatusField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemStatusField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


