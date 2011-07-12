<?php


class ComcastArrayOfReleaseField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfReleaseField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastReleaseField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


