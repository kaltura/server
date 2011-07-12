<?php


class ComcastArrayOfEncodingProfileField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfEncodingProfileField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfileField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


