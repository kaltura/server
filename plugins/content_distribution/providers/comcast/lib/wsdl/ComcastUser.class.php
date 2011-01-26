<?php


class ComcastUser extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfUserField';
			case 'authenticationMethod':
				return 'ComcastAuthorizationMethod';
			case 'permissionIDs':
				return 'ComcastIDSet';
			case 'timeZone':
				return 'ComcastTimeZone';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfUserField
	 **/
	public $template;
				
	/**
	 * @var ComcastAuthorizationMethod
	 **/
	public $authenticationMethod;
				
	/**
	 * @var string
	 **/
	public $emailAddress;
				
	/**
	 * @var long
	 **/
	public $failedSignInAttempts;
				
	/**
	 * @var long
	 **/
	public $lastAccountID;
				
	/**
	 * @var dateTime
	 **/
	public $lastFailedSignInAttempt;
				
	/**
	 * @var string
	 **/
	public $lastFailedSignInAttemptIPAddress;
				
	/**
	 * @var string
	 **/
	public $name;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $permissionIDs;
				
	/**
	 * @var dateTime
	 **/
	public $possiblePasswordAttackDetected;
				
	/**
	 * @var boolean
	 **/
	public $preventPasswordAttacks;
				
	/**
	 * @var ComcastTimeZone
	 **/
	public $timeZone;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


