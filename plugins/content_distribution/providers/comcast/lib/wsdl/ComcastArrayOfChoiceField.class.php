<?php


class ComcastArrayOfChoiceField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfChoiceField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastChoiceField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


