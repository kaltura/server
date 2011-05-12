<?php


class ComcastMediaList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMedia");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


