<?php


class ComcastArrayOfRequestField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfRequestField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRequestField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


