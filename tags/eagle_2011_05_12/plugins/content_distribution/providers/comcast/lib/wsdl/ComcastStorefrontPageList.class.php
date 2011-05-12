<?php


class ComcastStorefrontPageList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPage");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


