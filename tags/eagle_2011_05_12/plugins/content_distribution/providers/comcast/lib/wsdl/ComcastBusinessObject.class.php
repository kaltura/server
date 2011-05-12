<?php


abstract class ComcastBusinessObject extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'customData':
				return 'ComcastCustomData';
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
	public $ID;
				
	/**
	 * @var dateTime
	 **/
	public $added;
				
	/**
	 * @var string
	 **/
	public $addedByUser;
				
	/**
	 * @var string
	 **/
	public $addedByUserEmailAddress;
				
	/**
	 * @var long
	 **/
	public $addedByUserID;
				
	/**
	 * @var string
	 **/
	public $addedByUserName;
				
	/**
	 * @var string
	 **/
	public $description;
				
	/**
	 * @var dateTime
	 **/
	public $lastModified;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUser;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUserEmailAddress;
				
	/**
	 * @var long
	 **/
	public $lastModifiedByUserID;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUserName;
				
	/**
	 * @var boolean
	 **/
	public $locked;
				
	/**
	 * @var string
	 **/
	public $owner;
				
	/**
	 * @var long
	 **/
	public $ownerAccountID;
				
	/**
	 * @var int
	 **/
	public $version;
				
	/**
	 * @var ComcastCustomData
	 **/
	public $customData;
				
}


