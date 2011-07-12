<?php


class ComcastEncodingProfileList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:EncodingProfileList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfile");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


