<?php


class ComcastReleaseList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRelease");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


