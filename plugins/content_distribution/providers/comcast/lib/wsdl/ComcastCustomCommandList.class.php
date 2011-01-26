<?php


class ComcastCustomCommandList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommand");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


