<?php


class ComcastSystemTaskList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemTask");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


