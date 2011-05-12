<?php


class ComcastCustomField extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCustomFieldField';
			case 'allowedTextValues':
				return 'ComcastArrayOfstring';
			case 'fieldType':
				return 'ComcastCustomFieldType';
			case 'limitToAPIObjects':
				return 'ComcastArrayOfAPIObject';
			case 'shareWithAccountIDs':
				return 'ComcastIDSet';
			case 'shareWithAccounts':
				return 'ComcastArrayOfstring';
			case 'supportedFormats':
				return 'ComcastArrayOfFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfCustomFieldField
	 **/
	public $template;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedTextValues;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var string
	 **/
	public $defaultTextValue;
				
	/**
	 * @var string
	 **/
	public $fieldName;
				
	/**
	 * @var ComcastCustomFieldType
	 **/
	public $fieldType;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var boolean
	 **/
	public $includeInMetafiles;
				
	/**
	 * @var boolean
	 **/
	public $includeInReleases;
				
	/**
	 * @var int
	 **/
	public $length;
				
	/**
	 * @var ComcastArrayOfAPIObject
	 **/
	public $limitToAPIObjects;
				
	/**
	 * @var int
	 **/
	public $linesToDisplay;
				
	/**
	 * @var string
	 **/
	public $namespace;
				
	/**
	 * @var string
	 **/
	public $namespacePrefix;
				
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
	 * @var boolean
	 **/
	public $showInMoreFields;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $supportedFormats;
				
	/**
	 * @var string
	 **/
	public $title;
				
}


