<?php


class ComcastArrayOfCategoryField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCategoryField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


