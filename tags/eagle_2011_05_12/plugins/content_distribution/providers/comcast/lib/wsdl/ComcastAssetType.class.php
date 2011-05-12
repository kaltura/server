<?php


class ComcastAssetType extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfAssetTypeField';
			case 'shareWithAccountIDs':
				return 'ComcastIDSet';
			case 'shareWithAccounts':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfAssetTypeField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $applyByDefault;
				
	/**
	 * @var boolean
	 **/
	public $applyToThumbnailsByDefault;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $shareWithAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $shareWithAccounts;
				
	/**
	 * @var boolean
	 **/
	public $shareWithAllAccounts;
				
	/**
	 * @var string
	 **/
	public $title;
				
}


