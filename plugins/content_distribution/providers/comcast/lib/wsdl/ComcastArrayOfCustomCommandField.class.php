<?php


class ComcastArrayOfCustomCommandField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommandField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


