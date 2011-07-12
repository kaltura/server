<?php


class ComcastMediaList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:MediaList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastMedia");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


