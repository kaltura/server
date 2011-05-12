<?php


class ComcastPriceList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPrice");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


