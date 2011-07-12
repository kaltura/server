<?php


class ComcastArrayOfMediaFileField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfMediaFileField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastMediaFileField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


