<?php


class ComcastArrayOfCustomFieldField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomFieldField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


