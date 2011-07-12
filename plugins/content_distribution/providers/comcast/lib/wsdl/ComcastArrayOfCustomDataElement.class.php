<?php


class ComcastArrayOfCustomDataElement extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:ArrayOfCustomDataElement';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCustomDataElement");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


