<?php


class ComcastChoiceList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastChoice");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


