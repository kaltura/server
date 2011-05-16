<?php


class ComcastPlaylistSort extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/sort/:PlaylistSort';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastPlaylistField';
			case 'tieBreaker':
				return 'ComcastPlaylistSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastPlaylistField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastPlaylistSort
	 **/
	public $tieBreaker;
				
}


