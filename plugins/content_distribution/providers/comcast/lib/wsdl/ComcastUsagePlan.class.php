<?php


class ComcastUsagePlan extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfUsagePlanField';
			case 'allowedAccountIDs':
				return 'ComcastIDSet';
			case 'allowedAccountNames':
				return 'ComcastArrayOfstring';
			case 'categoryIDs':
				return 'ComcastIDSet';
			case 'mediaIDs':
				return 'ComcastIDSet';
			case 'playlistIDs':
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
	 * @var ComcastArrayOfUsagePlanField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $allowBrowsing;
				
	/**
	 * @var boolean
	 **/
	public $allowCustomServerReleases;
				
	/**
	 * @var boolean
	 **/
	public $allowDownloads;
				
	/**
	 * @var boolean
	 **/
	public $allowPushing;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $allowedAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedAccountNames;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $categoryIDs;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $playlistIDs;
				
	/**
	 * @var string
	 **/
	public $title;
				
}


