<?php


class ComcastArrayOfSystemTaskField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemTaskField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


