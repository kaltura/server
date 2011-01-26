<?php


class ComcastDirectoryList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDirectory");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


