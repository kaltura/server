<?php


class ComcastCategoryList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCategory");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


