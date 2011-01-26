<?php


class ComcastLicense extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfLicenseField';
			case 'appliesTo':
				return 'ComcastDelivery';
			case 'authentication':
				return 'ComcastAuthentication';
			case 'categoryIDs':
				return 'ComcastIDSet';
			case 'directories':
				return 'ComcastArrayOfstring';
			case 'directoryIDs':
				return 'ComcastIDSet';
			case 'endUserIDs':
				return 'ComcastIDSet';
			case 'endUserNames':
				return 'ComcastArrayOfstring';
			case 'endUserPermissionIDs':
				return 'ComcastIDSet';
			case 'expirationTimeAfterFirstUseUnits':
				return 'ComcastTimeUnits';
			case 'expirationTimeUnits':
				return 'ComcastTimeUnits';
			case 'externalGroups':
				return 'ComcastArrayOfstring';
			case 'formats':
				return 'ComcastArrayOfFormat';
			case 'mediaIDs':
				return 'ComcastIDSet';
			case 'playlistIDs':
				return 'ComcastIDSet';
			case 'subscriptionGracePeriodUnits':
				return 'ComcastTimeUnits';
			case 'subscriptionTrialPeriodUnits':
				return 'ComcastTimeUnits';
			case 'timeAllowedUnits':
				return 'ComcastTimeUnits';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfLicenseField
	 **/
	public $template;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $appliesTo;
				
	/**
	 * @var ComcastAuthentication
	 **/
	public $authentication;
				
	/**
	 * @var string
	 **/
	public $authenticationURL;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyRenewByDefault;
				
	/**
	 * @var dateTime
	 **/
	public $availableDate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $categoryIDs;
				
	/**
	 * @var float
	 **/
	public $defaultInitialPrice;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $directories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $directoryIDs;
				
	/**
	 * @var boolean
	 **/
	public $disableBackups;
				
	/**
	 * @var boolean
	 **/
	public $disableOnClockRollback;
				
	/**
	 * @var boolean
	 **/
	public $disableOnPC;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $drmKeyID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $endUserIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $endUserNames;
				
	/**
	 * @var long
	 **/
	public $endUserPermissionCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $endUserPermissionIDs;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var int
	 **/
	public $expirationTime;
				
	/**
	 * @var int
	 **/
	public $expirationTimeAfterFirstUse;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $expirationTimeAfterFirstUseUnits;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $expirationTimeUnits;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $externalGroups;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $formats;
				
	/**
	 * @var long
	 **/
	public $highestBitrate;
				
	/**
	 * @var long
	 **/
	public $licensesPerEndUser;
				
	/**
	 * @var long
	 **/
	public $lowestBitrate;
				
	/**
	 * @var long
	 **/
	public $maximumBurns;
				
	/**
	 * @var long
	 **/
	public $maximumPlays;
				
	/**
	 * @var long
	 **/
	public $maximumRenewals;
				
	/**
	 * @var long
	 **/
	public $maximumTransfersToDevice;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaIDs;
				
	/**
	 * @var long
	 **/
	public $minimumRenewals;
				
	/**
	 * @var string
	 **/
	public $parentLicense;
				
	/**
	 * @var long
	 **/
	public $parentLicenseID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $playlistIDs;
				
	/**
	 * @var boolean
	 **/
	public $requireIndividualization;
				
	/**
	 * @var boolean
	 **/
	public $requireSecurePlayer;
				
	/**
	 * @var boolean
	 **/
	public $showInPicker;
				
	/**
	 * @var int
	 **/
	public $subscriptionGracePeriod;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $subscriptionGracePeriodUnits;
				
	/**
	 * @var int
	 **/
	public $subscriptionTrialPeriod;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $subscriptionTrialPeriodUnits;
				
	/**
	 * @var long
	 **/
	public $templateLicenseID;
				
	/**
	 * @var string
	 **/
	public $templateLicenseTitle;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var int
	 **/
	public $timeAllowed;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $timeAllowedUnits;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $useDRM;
				
}


