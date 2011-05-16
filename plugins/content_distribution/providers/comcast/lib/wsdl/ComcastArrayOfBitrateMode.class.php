<?php


class ComcastArrayOfBitrateMode extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfBitrateMode';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastBitrateMode");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


