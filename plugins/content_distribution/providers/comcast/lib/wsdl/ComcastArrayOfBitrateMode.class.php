<?php


class ComcastArrayOfBitrateMode extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastBitrateMode");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


