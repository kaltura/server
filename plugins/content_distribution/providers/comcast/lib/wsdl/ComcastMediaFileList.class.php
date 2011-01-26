<?php


class ComcastMediaFileList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaFile");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


