<?php


class ComcastDirectoryList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:DirectoryList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastDirectory");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


