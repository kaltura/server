<?php


class ComcastPlaylist extends ComcastContent
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:Playlist';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPlaylistField';
			case 'choiceIDs':
				return 'ComcastIDList';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfPlaylistField
	 **/
	public $template;
				
	/**
	 * @var long
	 **/
	public $choiceCount;
				
	/**
	 * @var ComcastIDList
	 **/
	public $choiceIDs;
				
	/**
	 * @var boolean
	 **/
	public $shufflePlay;
				
}


