<?php


class ComcastPlaylistList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:PlaylistList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPlaylist");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


