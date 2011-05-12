<?php


class ComcastArrayOfPlaylistField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPlaylistField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


