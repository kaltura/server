<?php


class ComcastPermission extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPermissionField';
			case 'roleIDs':
				return 'ComcastIDSet';
			case 'roleTitles':
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
	 * @var ComcastArrayOfPermissionField
	 **/
	public $template;
				
	/**
	 * @var long
	 **/
	public $accountID;
				
	/**
	 * @var boolean
	 **/
	public $applyToSubAccounts;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $roleIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $roleTitles;
				
	/**
	 * @var boolean
	 **/
	public $showHomeTab;
				
	/**
	 * @var dateTime
	 **/
	public $userAdded;
				
	/**
	 * @var string
	 **/
	public $userEmailAddress;
				
	/**
	 * @var long
	 **/
	public $userID;
				
	/**
	 * @var string
	 **/
	public $userName;
				
	/**
	 * @var string
	 **/
	public $userOwner;
				
}


