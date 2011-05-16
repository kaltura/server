<?php


class ComcastPossibleReleaseSettings extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:PossibleReleaseSettings';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPossibleReleaseSetting");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


