<?php


class ComcastCustomFieldList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


