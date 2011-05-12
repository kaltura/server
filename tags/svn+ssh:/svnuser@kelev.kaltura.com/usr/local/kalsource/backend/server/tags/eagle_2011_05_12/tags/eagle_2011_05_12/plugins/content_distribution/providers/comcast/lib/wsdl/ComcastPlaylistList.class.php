<?php


class ComcastPlaylistList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPlaylist");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


