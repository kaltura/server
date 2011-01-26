<?php


class ComcastEncodingProfileList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfile");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


