<?php


class ComcastChoiceList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:ChoiceList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastChoice");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


