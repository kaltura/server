<?php


class ComcastPortalList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPortal");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


