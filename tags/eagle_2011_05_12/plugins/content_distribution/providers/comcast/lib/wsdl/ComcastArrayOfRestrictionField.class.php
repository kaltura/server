<?php


class ComcastArrayOfRestrictionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRestrictionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


