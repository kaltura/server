<?php


class ComcastArrayOfMediaField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfMediaField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastMediaField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


