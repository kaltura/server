<?php


class ComcastEndUser extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfEndUserField';
			case 'authenticationMethod':
				return 'ComcastAuthorizationMethod';
			case 'creditCardStatus':
				return 'ComcastStatus';
			case 'endUserPermissionIDs':
				return 'ComcastIDSet';
			case 'licenseIDs':
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
	 * @var ComcastArrayOfEndUserField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $acceptedLicenseAgreement;
				
	/**
	 * @var string
	 **/
	public $address;
				
	/**
	 * @var string
	 **/
	public $alternatePhoneNumber;
				
	/**
	 * @var ComcastAuthorizationMethod
	 **/
	public $authenticationMethod;
				
	/**
	 * @var string
	 **/
	public $city;
				
	/**
	 * @var string
	 **/
	public $company;
				
	/**
	 * @var string
	 **/
	public $country;
				
	/**
	 * @var int
	 **/
	public $creditCardExpirationMonth;
				
	/**
	 * @var int
	 **/
	public $creditCardExpirationYear;
				
	/**
	 * @var string
	 **/
	public $creditCardInfo;
				
	/**
	 * @var string
	 **/
	public $creditCardNumber;
				
	/**
	 * @var ComcastStatus
	 **/
	public $creditCardStatus;
				
	/**
	 * @var string
	 **/
	public $creditCardToken;
				
	/**
	 * @var dateTime
	 **/
	public $creditCardTokenGenerated;
				
	/**
	 * @var string
	 **/
	public $creditCardType;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $displayName;
				
	/**
	 * @var string
	 **/
	public $emailAddress;
				
	/**
	 * @var long
	 **/
	public $endUserPermissionCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $endUserPermissionIDs;
				
	/**
	 * @var string
	 **/
	public $firstName;
				
	/**
	 * @var string
	 **/
	public $lastName;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $licenseIDs;
				
	/**
	 * @var string
	 **/
	public $nameOnCreditCard;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var string
	 **/
	public $phoneNumber;
				
	/**
	 * @var string
	 **/
	public $postalCode;
				
	/**
	 * @var string
	 **/
	public $state;
				
	/**
	 * @var ComcastTimeZone
	 **/
	public $timeZone;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


