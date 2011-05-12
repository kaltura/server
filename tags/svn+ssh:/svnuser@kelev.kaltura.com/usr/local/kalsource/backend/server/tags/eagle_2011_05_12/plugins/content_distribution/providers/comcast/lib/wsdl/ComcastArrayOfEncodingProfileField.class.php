<?php


class ComcastArrayOfEncodingProfileField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfileField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


