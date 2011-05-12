<?php


class ComcastPossibleReleaseSettings extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPossibleReleaseSetting");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


