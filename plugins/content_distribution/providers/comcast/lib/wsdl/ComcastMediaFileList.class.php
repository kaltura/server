<?php


class ComcastMediaFileList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:MediaFileList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastMediaFile");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


