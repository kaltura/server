<?php


class ComcastAccountList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAccount");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


