<?php


class ComcastCodecs extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:Codecs';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCodec");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


