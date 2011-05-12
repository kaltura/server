<?php


class ComcastArrayOfDirectoryField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDirectoryField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


