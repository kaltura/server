<?php


class ComcastStorefrontList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefront");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


