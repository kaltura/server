<?php


class ComcastAddContentResults extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:AddContentResults';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'mediaFileIDs':
				return 'ComcastIDList';
			case 'releaseIDs':
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
	 * @var long
	 **/
	public $mediaID;
				
	/**
	 * @var ComcastIDList
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var ComcastIDList
	 **/
	public $releaseIDs;
				
}


