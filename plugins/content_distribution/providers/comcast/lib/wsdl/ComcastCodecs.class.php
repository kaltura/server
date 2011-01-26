<?php


class ComcastCodecs extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCodec");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


