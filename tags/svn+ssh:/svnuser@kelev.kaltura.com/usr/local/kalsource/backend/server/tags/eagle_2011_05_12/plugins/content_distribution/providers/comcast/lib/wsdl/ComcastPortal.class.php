<?php


class ComcastPortal extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPortalField';
			case 'availableBitrates':
				return 'ComcastArrayOflong';
			case 'availableDelivery':
				return 'ComcastDelivery';
			case 'availableFormats':
				return 'ComcastArrayOfFormat';
			case 'defaultFormat':
				return 'ComcastFormat';
			case 'exclusiveFormats':
				return 'ComcastArrayOfFormat';
			case 'externalGroups':
				return 'ComcastArrayOfstring';
			case 'limitToAuthors':
				return 'ComcastArrayOfstring';
			case 'limitToCategories':
				return 'ComcastArrayOfstring';
			case 'limitToCategoryIDs':
				return 'ComcastIDSet';
			case 'newWindowFormats':
				return 'ComcastArrayOfFormat';
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
			case 'searchCategories':
				return 'ComcastArrayOfstring';
			case 'searchCategoryIDs':
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
	 * @var ComcastArrayOfPortalField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $PID;
				
	/**
	 * @var long
	 **/
	public $RSSHash;
				
	/**
	 * @var dateTime
	 **/
	public $RSSLastModified;
				
	/**
	 * @var string
	 **/
	public $RSSURL;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var string
	 **/
	public $airdateFormat;
				
	/**
	 * @var string
	 **/
	public $airdateLabel;
				
	/**
	 * @var boolean
	 **/
	public $allowAirdateSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowApproval;
				
	/**
	 * @var boolean
	 **/
	public $allowApprovedSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowAuthorSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowCategorySearching;
				
	/**
	 * @var boolean
	 **/
	public $allowDescriptionSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowFullScreen;
				
	/**
	 * @var boolean
	 **/
	public $allowKeywordSearching;
				
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
	public $allowTitleSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowTranscriptSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowUserNameEditing;
				
	/**
	 * @var string
	 **/
	public $alternatePhoneNumberLabel;
				
	/**
	 * @var string
	 **/
	public $authorLabel;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $availableBitrates;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $availableDelivery;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $availableFormats;
				
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
	public $categoryLabel;
				
	/**
	 * @var string
	 **/
	public $customerServiceEmailAddress;
				
	/**
	 * @var string
	 **/
	public $customerServiceEmailSignature;
				
	/**
	 * @var long
	 **/
	public $defaultBitrate;
				
	/**
	 * @var ComcastFormat
	 **/
	public $defaultFormat;
				
	/**
	 * @var string
	 **/
	public $descriptionLabel;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $endUserLicenseAgreement;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $exclusiveFormats;
				
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
	 * @var int
	 **/
	public $itemsPerPage;
				
	/**
	 * @var string
	 **/
	public $keywordsLabel;
				
	/**
	 * @var string
	 **/
	public $leftFrameURL;
				
	/**
	 * @var int
	 **/
	public $leftFrameWidth;
				
	/**
	 * @var boolean
	 **/
	public $limitByEndUserLocation;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToAuthors;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToCategories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $limitToCategoryIDs;
				
	/**
	 * @var boolean
	 **/
	public $limitToProtectedReleases;
				
	/**
	 * @var int
	 **/
	public $minimumPasswordLength;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $newWindowFormats;
				
	/**
	 * @var int
	 **/
	public $newWindowHeight;
				
	/**
	 * @var int
	 **/
	public $newWindowWidth;
				
	/**
	 * @var string
	 **/
	public $phoneNumberLabel;
				
	/**
	 * @var int
	 **/
	public $playerHeight;
				
	/**
	 * @var boolean
	 **/
	public $playerOnLeft;
				
	/**
	 * @var boolean
	 **/
	public $playerStretchToFit;
				
	/**
	 * @var string
	 **/
	public $playerURL;
				
	/**
	 * @var int
	 **/
	public $playerWidth;
				
	/**
	 * @var boolean
	 **/
	public $promptForPreferences;
				
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
	 * @var ComcastArrayOfstring
	 **/
	public $searchCategories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $searchCategoryIDs;
				
	/**
	 * @var boolean
	 **/
	public $sendSignInConfirmation;
				
	/**
	 * @var boolean
	 **/
	public $showAirdate;
				
	/**
	 * @var boolean
	 **/
	public $showApprovedReleases;
				
	/**
	 * @var boolean
	 **/
	public $showAuthor;
				
	/**
	 * @var boolean
	 **/
	public $showBitrate;
				
	/**
	 * @var boolean
	 **/
	public $showFormat;
				
	/**
	 * @var boolean
	 **/
	public $showGlobalContent;
				
	/**
	 * @var boolean
	 **/
	public $showPlayer;
				
	/**
	 * @var boolean
	 **/
	public $showReleaseURL;
				
	/**
	 * @var boolean
	 **/
	public $showTranscriptBelowPlayer;
				
	/**
	 * @var boolean
	 **/
	public $showUnapprovedReleases;
				
	/**
	 * @var boolean
	 **/
	public $sortDescending;
				
	/**
	 * @var string
	 **/
	public $sortKey;
				
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
	 * @var string
	 **/
	public $titleLabel;
				
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
	public $trackBrowser;
				
	/**
	 * @var boolean
	 **/
	public $trackLocation;
				
	/**
	 * @var string
	 **/
	public $transcriptLabel;
				
	/**
	 * @var boolean
	 **/
	public $useDirectories;
				
	/**
	 * @var boolean
	 **/
	public $useEmailAddressAsUserName;
				
}


