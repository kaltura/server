<?php


class ComcastJobList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastJob");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


