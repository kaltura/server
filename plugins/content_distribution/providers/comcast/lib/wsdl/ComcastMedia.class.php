<?php


class ComcastMedia extends ComcastContent
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfMediaField';
			case 'choiceIDs':
				return 'ComcastIDSet';
			case 'mediaFileIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfMediaField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $album;
				
	/**
	 * @var boolean
	 **/
	public $allowFastForwarding;
				
	/**
	 * @var boolean
	 **/
	public $canDelete;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $choiceIDs;
				
	/**
	 * @var string
	 **/
	public $genre;
				
	/**
	 * @var long
	 **/
	public $mediaFileCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var long
	 **/
	public $thumbnailMediaFileID;
				
}


