<?php


class ComcastStorefront extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfStorefrontField';
			case 'externalGroups':
				return 'ComcastArrayOfstring';
			case 'requireAddress':
				return 'ComcastContactInfo';
			case 'requireAlternatePhoneNumber':
				return 'ComcastContactInfo';
			case 'requireCity':
				return 'ComcastContactInfo';
			case 'requireCompany':
				return 'ComcastContactInfo';
			case 'requireCountry':
				return 'ComcastContactInfo';
			case 'requireEmailAddress':
				return 'ComcastContactInfo';
			case 'requireFirstName':
				return 'ComcastContactInfo';
			case 'requireLastName':
				return 'ComcastContactInfo';
			case 'requirePassword':
				return 'ComcastContactInfo';
			case 'requirePhoneNumber':
				return 'ComcastContactInfo';
			case 'requirePostalCode':
				return 'ComcastContactInfo';
			case 'requireState':
				return 'ComcastContactInfo';
			case 'storefrontPageIDs':
				return 'ComcastIDSet';
			case 'storefrontPageTitles':
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
	 * @var ComcastArrayOfStorefrontField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $PID;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var string
	 **/
	public $airdateFormat;
				
	/**
	 * @var boolean
	 **/
	public $allowSelfEditing;
				
	/**
	 * @var boolean
	 **/
	public $allowSelfRegistration;
				
	/**
	 * @var boolean
	 **/
	public $allowSignInRecovery;
				
	/**
	 * @var boolean
	 **/
	public $allowSignOut;
				
	/**
	 * @var boolean
	 **/
	public $allowUserNameEditing;
				
	/**
	 * @var string
	 **/
	public $alternatePhoneNumberLabel;
				
	/**
	 * @var int
	 **/
	public $bottomFrameHeight;
				
	/**
	 * @var string
	 **/
	public $bottomFrameURL;
				
	/**
	 * @var string
	 **/
	public $customerServiceEmailAddress;
				
	/**
	 * @var string
	 **/
	public $customerServiceEmailSignature;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $endUserLicenseAgreement;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $externalGroups;
				
	/**
	 * @var boolean
	 **/
	public $hasEndUserLicenseAgreement;
				
	/**
	 * @var int
	 **/
	public $headerHeight;
				
	/**
	 * @var string
	 **/
	public $leftFrameURL;
				
	/**
	 * @var int
	 **/
	public $leftFrameWidth;
				
	/**
	 * @var int
	 **/
	public $minimumPasswordLength;
				
	/**
	 * @var string
	 **/
	public $phoneNumberLabel;
				
	/**
	 * @var string
	 **/
	public $purchaseNotificationPassword;
				
	/**
	 * @var string
	 **/
	public $purchaseNotificationURL;
				
	/**
	 * @var string
	 **/
	public $purchaseNotificationUserName;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireAddress;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireAlternatePhoneNumber;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireCity;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireCompany;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireCountry;
				
	/**
	 * @var boolean
	 **/
	public $requireCreditCard;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireEmailAddress;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireFirstName;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireLastName;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requirePassword;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requirePhoneNumber;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requirePostalCode;
				
	/**
	 * @var boolean
	 **/
	public $requireSignIn;
				
	/**
	 * @var boolean
	 **/
	public $requireSignInConfirmation;
				
	/**
	 * @var ComcastContactInfo
	 **/
	public $requireState;
				
	/**
	 * @var string
	 **/
	public $rightFrameURL;
				
	/**
	 * @var int
	 **/
	public $rightFrameWidth;
				
	/**
	 * @var boolean
	 **/
	public $sendPaymentFailureEmails;
				
	/**
	 * @var boolean
	 **/
	public $sendReceipts;
				
	/**
	 * @var boolean
	 **/
	public $sendSignInConfirmation;
				
	/**
	 * @var string
	 **/
	public $shoppingCartImageURL;
				
	/**
	 * @var boolean
	 **/
	public $showAirdate;
				
	/**
	 * @var boolean
	 **/
	public $showAuthor;
				
	/**
	 * @var boolean
	 **/
	public $showPurchaseNotificationURLResponse;
				
	/**
	 * @var long
	 **/
	public $storefrontPageCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $storefrontPageIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $storefrontPageTitles;
				
	/**
	 * @var string
	 **/
	public $stylesheetURL;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var int
	 **/
	public $topFrameHeight;
				
	/**
	 * @var string
	 **/
	public $topFrameURL;
				
	/**
	 * @var boolean
	 **/
	public $useEmailAddressAsUserName;
				
}


