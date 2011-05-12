<?php


class ComcastArrayOfFormat extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastFormat");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


