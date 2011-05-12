<?php


class ComcastArrayOfMediaFileField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaFileField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


