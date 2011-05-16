<?php


class ComcastArrayOfPlaylistField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:ArrayOfPlaylistField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPlaylistField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


