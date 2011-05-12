<?php


class ComcastCategory extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCategoryField';
			case 'defaultLicenseIDs':
				return 'ComcastIDSet';
			case 'defaultLicenses':
				return 'ComcastArrayOfstring';
			case 'defaultRestrictionIDs':
				return 'ComcastIDSet';
			case 'defaultRestrictions':
				return 'ComcastArrayOfstring';
			case 'defaultUsagePlanIDs':
				return 'ComcastIDSet';
			case 'defaultUsagePlans':
				return 'ComcastArrayOfstring';
			case 'depth':
				return 'Comcastint';
			case 'index':
				return 'Comcastint';
			case 'mediaIDs':
				return 'ComcastIDSet';
			case 'playlistIDs':
				return 'ComcastIDSet';
			case 'treeOrder':
				return 'Comcastint';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfCategoryField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultLicenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultLicenses;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultRestrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultRestrictions;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultUsagePlans;
				
	/**
	 * @var Comcastint
	 **/
	public $depth;
				
	/**
	 * @var string
	 **/
	public $fullTitle;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var boolean
	 **/
	public $hasChildren;
				
	/**
	 * @var Comcastint
	 **/
	public $index;
				
	/**
	 * @var string
	 **/
	public $label;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaIDs;
				
	/**
	 * @var string
	 **/
	public $parentCategory;
				
	/**
	 * @var long
	 **/
	public $parentCategoryId;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $playlistIDs;
				
	/**
	 * @var string
	 **/
	public $scheme;
				
	/**
	 * @var boolean
	 **/
	public $showInPicker;
				
	/**
	 * @var boolean
	 **/
	public $showInPortals;
				
	/**
	 * @var string
	 **/
	public $sortByPlaylist;
				
	/**
	 * @var long
	 **/
	public $sortByPlaylistID;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var Comcastint
	 **/
	public $treeOrder;
				
}


