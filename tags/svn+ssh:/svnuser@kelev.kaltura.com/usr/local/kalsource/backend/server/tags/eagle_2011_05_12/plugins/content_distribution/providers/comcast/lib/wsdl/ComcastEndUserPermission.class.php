<?php


class ComcastEndUserPermission extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfEndUserPermissionField';
			case 'creditCardType':
				return 'ComcastCreditCardType';
			case 'licenseMediaIDs':
				return 'ComcastIDSet';
			case 'licensePlaylistIDs':
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
	 * @var ComcastArrayOfEndUserPermissionField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyRenew;
				
	/**
	 * @var dateTime
	 **/
	public $availableDate;
				
	/**
	 * @var string
	 **/
	public $couponCode;
				
	/**
	 * @var string
	 **/
	public $creditCardInfo;
				
	/**
	 * @var ComcastCreditCardType
	 **/
	public $creditCardType;
				
	/**
	 * @var long
	 **/
	public $currentPlays;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var long
	 **/
	public $endUserID;
				
	/**
	 * @var string
	 **/
	public $endUserName;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var string
	 **/
	public $externalID;
				
	/**
	 * @var dateTime
	 **/
	public $grantDate;
				
	/**
	 * @var dateTime
	 **/
	public $lastRenewed;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $licenseMediaIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $licensePlaylistIDs;
				
	/**
	 * @var string
	 **/
	public $licenseTitle;
				
	/**
	 * @var long
	 **/
	public $licensesGranted;
				
	/**
	 * @var boolean
	 **/
	public $prepaid;
				
	/**
	 * @var long
	 **/
	public $priceID;
				
	/**
	 * @var long
	 **/
	public $remainingLicenses;
				
	/**
	 * @var long
	 **/
	public $remainingPlays;
				
	/**
	 * @var boolean
	 **/
	public $renew;
				
	/**
	 * @var boolean
	 **/
	public $renewable;
				
	/**
	 * @var long
	 **/
	public $renewals;
				
	/**
	 * @var boolean
	 **/
	public $retryLastPayment;
				
	/**
	 * @var float
	 **/
	public $salesTaxRate;
				
	/**
	 * @var long
	 **/
	public $storefrontID;
				
	/**
	 * @var string
	 **/
	public $storefrontTitle;
				
	/**
	 * @var long
	 **/
	public $templateLicenseID;
				
	/**
	 * @var long
	 **/
	public $totalPlays;
				
}


