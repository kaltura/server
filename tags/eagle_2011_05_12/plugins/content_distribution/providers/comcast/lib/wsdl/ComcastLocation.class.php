<?php


class ComcastLocation extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfLocationField';
			case 'delivery':
				return 'ComcastDelivery';
			case 'mediaFileIDs':
				return 'ComcastIDSet';
			case 'storageNetworks':
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
	 * @var ComcastArrayOfLocationField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var boolean
	 **/
	public $hasSubstitutionURL;
				
	/**
	 * @var boolean
	 **/
	public $inUse;
				
	/**
	 * @var boolean
	 **/
	public $isPublic;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var string
	 **/
	public $privateKey;
				
	/**
	 * @var boolean
	 **/
	public $promptsToDownload;
				
	/**
	 * @var boolean
	 **/
	public $requireActiveFTP;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $storageNetworks;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


