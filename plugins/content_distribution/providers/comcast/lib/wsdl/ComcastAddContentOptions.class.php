<?php


class ComcastAddContentOptions extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'encodingProfileTitles':
				return 'ComcastArrayOfstring';
			case 'createReleases':
				return 'ComcastDelivery';
			case 'releaseDefaults':
				return 'ComcastRelease';
			case 'releaseServerIDs':
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
	 * @var boolean
	 **/
	public $generateThumbnail;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $encodingProfileTitles;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $createReleases;
				
	/**
	 * @var string
	 **/
	public $releaseOutletAccount;
				
	/**
	 * @var ComcastRelease
	 **/
	public $releaseDefaults;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseServerIDs;
				
	/**
	 * @var boolean
	 **/
	public $publish;
				
	/**
	 * @var boolean
	 **/
	public $deleteSource;
				
}


