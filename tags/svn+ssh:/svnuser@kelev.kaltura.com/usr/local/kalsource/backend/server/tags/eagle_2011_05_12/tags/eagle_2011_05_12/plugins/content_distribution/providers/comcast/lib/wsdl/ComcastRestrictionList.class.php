<?php


class ComcastRestrictionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRestriction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


