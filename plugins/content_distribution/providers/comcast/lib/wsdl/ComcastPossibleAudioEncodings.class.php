<?php


class ComcastPossibleAudioEncodings extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPossibleAudioEncoding");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


