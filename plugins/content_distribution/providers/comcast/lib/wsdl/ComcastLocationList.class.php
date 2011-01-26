<?php


class ComcastLocationList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLocation");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


