<?php


class ComcastPossibleAudioEncodings extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:PossibleAudioEncodings';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPossibleAudioEncoding");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


