<?php


class ComcastRequestList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:RequestList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRequest");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


