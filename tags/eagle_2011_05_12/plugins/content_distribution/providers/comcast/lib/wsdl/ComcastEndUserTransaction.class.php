<?php


class ComcastEndUserTransaction extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfEndUserTransactionField';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'creditCardType':
				return 'ComcastCreditCardType';
			case 'endUserCountry':
				return 'ComcastCountry';
			case 'endUserTransactionType':
				return 'ComcastEndUserTransactionType';
			case 'externalIDs':
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
	 * @var ComcastArrayOfEndUserTransactionField
	 **/
	public $template;
				
	/**
	 * @var float
	 **/
	public $amountBillable;
				
	/**
	 * @var float
	 **/
	public $amountDue;
				
	/**
	 * @var float
	 **/
	public $amountPaidTotal;
				
	/**
	 * @var float
	 **/
	public $amountPaidWithCard;
				
	/**
	 * @var float
	 **/
	public $amountPaidWithoutCard;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyCollectPayment;
				
	/**
	 * @var boolean
	 **/
	public $collectPayment;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var string
	 **/
	public $contentOwner;
				
	/**
	 * @var long
	 **/
	public $contentOwnerAccountID;
				
	/**
	 * @var string
	 **/
	public $contentTitle;
				
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
	 * @var ComcastCountry
	 **/
	public $endUserCountry;
				
	/**
	 * @var string
	 **/
	public $endUserFirstName;
				
	/**
	 * @var long
	 **/
	public $endUserID;
				
	/**
	 * @var string
	 **/
	public $endUserLastName;
				
	/**
	 * @var string
	 **/
	public $endUserName;
				
	/**
	 * @var long
	 **/
	public $endUserPermissionID;
				
	/**
	 * @var string
	 **/
	public $endUserPostalCode;
				
	/**
	 * @var string
	 **/
	public $endUserState;
				
	/**
	 * @var ComcastEndUserTransactionType
	 **/
	public $endUserTransactionType;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $externalIDs;
				
	/**
	 * @var dateTime
	 **/
	public $lastPaymentCollected;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var string
	 **/
	public $licenseTitle;
				
	/**
	 * @var boolean
	 **/
	public $paidInFull;
				
	/**
	 * @var dateTime
	 **/
	public $posted;
				
	/**
	 * @var float
	 **/
	public $refundPartialPayment;
				
	/**
	 * @var boolean
	 **/
	public $refundPayment;
				
	/**
	 * @var long
	 **/
	public $relatedTransactionID;
				
	/**
	 * @var long
	 **/
	public $releaseID;
				
	/**
	 * @var long
	 **/
	public $renewalNumber;
				
	/**
	 * @var float
	 **/
	public $salesTax;
				
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
				
}


