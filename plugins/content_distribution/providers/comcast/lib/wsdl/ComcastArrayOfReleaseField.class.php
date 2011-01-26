<?php


class ComcastArrayOfReleaseField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastReleaseField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


