<?php


class ComcastAccount extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfAccountField';
			case 'allowConsoleAccess':
				return 'Comcastboolean';
			case 'allowedDeliverySites':
				return 'ComcastArrayOfstring';
			case 'allowedUserAgents':
				return 'ComcastArrayOfstring';
			case 'childAccountIDs':
				return 'ComcastIDSet';
			case 'contentPublishingIsPublic':
				return 'Comcastboolean';
			case 'contentPublishingNetworks':
				return 'ComcastArrayOfstring';
			case 'contentPublishingUseMessaging':
				return 'Comcastboolean';
			case 'defaultContainerPlaylistIDs':
				return 'ComcastIDSet';
			case 'defaultContainerPlaylists':
				return 'ComcastArrayOfstring';
			case 'defaultInheritedServerIDs':
				return 'ComcastIDSet';
			case 'defaultLanguage':
				return 'ComcastLanguage';
			case 'defaultLicenseIDs':
				return 'ComcastIDSet';
			case 'defaultLicenses':
				return 'ComcastArrayOfstring';
			case 'defaultRestrictionIDs':
				return 'ComcastIDSet';
			case 'defaultRestrictions':
				return 'ComcastArrayOfstring';
			case 'defaultTimeZone':
				return 'ComcastTimeZone';
			case 'defaultUsagePlanIDs':
				return 'ComcastIDSet';
			case 'defaultUsagePlans':
				return 'ComcastArrayOfstring';
			case 'dropFolderFilePatterns':
				return 'ComcastArrayOfstring';
			case 'inheritedServerIDs':
				return 'ComcastIDSet';
			case 'limitToRoleIDs':
				return 'ComcastIDSet';
			case 'limitToRoles':
				return 'ComcastArrayOfstring';
			case 'metafileEncoding':
				return 'ComcastEncoding';
			case 'notificationActions':
				return 'ComcastArrayOfNotificationAction';
			case 'notificationItems':
				return 'ComcastArrayOfAPIObject';
			case 'notificationNetworks':
				return 'ComcastArrayOfstring';
			case 'paymentFailureEmailAddresses':
				return 'ComcastArrayOfstring';
			case 'paymentGateway':
				return 'ComcastPaymentGateway';
			case 'permissionIDs':
				return 'ComcastIDSet';
			case 'possibleRatings':
				return 'ComcastArrayOfstring';
			case 'targetCountries':
				return 'ComcastArrayOfCountry';
			case 'visibleToAccountIDs':
				return 'ComcastIDSet';
			case 'visibleToAccounts':
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
	 * @var ComcastArrayOfAccountField
	 **/
	public $template;
				
	/**
	 * @var int
	 **/
	public $actionLimit;
				
	/**
	 * @var Comcastboolean
	 **/
	public $allowConsoleAccess;
				
	/**
	 * @var boolean
	 **/
	public $allowOpenDelivery;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedDeliverySites;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedUserAgents;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyCollectPaymentsByDefault;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyDeleteEmptyReleases;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyDeleteExpiredContent;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyGenerateThumbnails;
				
	/**
	 * @var string
	 **/
	public $bannerHTML;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $childAccountIDs;
				
	/**
	 * @var string
	 **/
	public $contactInfo;
				
	/**
	 * @var Comcastboolean
	 **/
	public $contentPublishingIsPublic;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentPublishingNetworks;
				
	/**
	 * @var string
	 **/
	public $contentPublishingPassword;
				
	/**
	 * @var string
	 **/
	public $contentPublishingURL;
				
	/**
	 * @var Comcastboolean
	 **/
	public $contentPublishingUseMessaging;
				
	/**
	 * @var string
	 **/
	public $contentPublishingUserName;
				
	/**
	 * @var boolean
	 **/
	public $defaultApproved;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultContainerPlaylistIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultContainerPlaylists;
				
	/**
	 * @var boolean
	 **/
	public $defaultContentApproved;
				
	/**
	 * @var string
	 **/
	public $defaultCopyright;
				
	/**
	 * @var long
	 **/
	public $defaultFLVDownloadServerID;
				
	/**
	 * @var long
	 **/
	public $defaultFLVPushServerID;
				
	/**
	 * @var long
	 **/
	public $defaultFLVStorageServerID;
				
	/**
	 * @var long
	 **/
	public $defaultFLVStreamingServerID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultInheritedServerIDs;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $defaultLanguage;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultLicenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultLicenses;
				
	/**
	 * @var long
	 **/
	public $defaultOtherDownloadServerID;
				
	/**
	 * @var long
	 **/
	public $defaultOtherPushServerID;
				
	/**
	 * @var long
	 **/
	public $defaultOtherStorageServerID;
				
	/**
	 * @var long
	 **/
	public $defaultOtherStreamingServerID;
				
	/**
	 * @var long
	 **/
	public $defaultQTDownloadServerID;
				
	/**
	 * @var long
	 **/
	public $defaultQTPushServerID;
				
	/**
	 * @var long
	 **/
	public $defaultQTStorageServerID;
				
	/**
	 * @var long
	 **/
	public $defaultQTStreamingServerID;
				
	/**
	 * @var string
	 **/
	public $defaultRating;
				
	/**
	 * @var long
	 **/
	public $defaultRealDownloadServerID;
				
	/**
	 * @var long
	 **/
	public $defaultRealPushServerID;
				
	/**
	 * @var long
	 **/
	public $defaultRealStorageServerID;
				
	/**
	 * @var long
	 **/
	public $defaultRealStreamingServerID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultRestrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultRestrictions;
				
	/**
	 * @var long
	 **/
	public $defaultThumbnailServerID;
				
	/**
	 * @var ComcastTimeZone
	 **/
	public $defaultTimeZone;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultUsagePlans;
				
	/**
	 * @var long
	 **/
	public $defaultWMDownloadServerID;
				
	/**
	 * @var long
	 **/
	public $defaultWMPushServerID;
				
	/**
	 * @var long
	 **/
	public $defaultWMStorageServerID;
				
	/**
	 * @var long
	 **/
	public $defaultWMStreamingServerID;
				
	/**
	 * @var boolean
	 **/
	public $disableAccessToReleasedMediaFileURLs;
				
	/**
	 * @var boolean
	 **/
	public $disableDropFolder;
				
	/**
	 * @var boolean
	 **/
	public $disableMediaFileEncoding;
				
	/**
	 * @var boolean
	 **/
	public $disableNewAccounts;
				
	/**
	 * @var boolean
	 **/
	public $disableNewDRMLicenses;
				
	/**
	 * @var boolean
	 **/
	public $disableNewLicenses;
				
	/**
	 * @var boolean
	 **/
	public $disableNewSharing;
				
	/**
	 * @var boolean
	 **/
	public $disablePortals;
				
	/**
	 * @var boolean
	 **/
	public $disableStandAloneTracking;
				
	/**
	 * @var boolean
	 **/
	public $disableStandAloneUploads;
				
	/**
	 * @var boolean
	 **/
	public $disableStorefronts;
				
	/**
	 * @var boolean
	 **/
	public $disableThumbnailGeneration;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $domain;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $dropFolderFilePatterns;
				
	/**
	 * @var string
	 **/
	public $errorMessageBaseURL;
				
	/**
	 * @var boolean
	 **/
	public $hasChildAccounts;
				
	/**
	 * @var string
	 **/
	public $helpURL;
				
	/**
	 * @var long
	 **/
	public $homeTabHeaderHeight;
				
	/**
	 * @var string
	 **/
	public $homeTabURL;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $inheritedServerIDs;
				
	/**
	 * @var boolean
	 **/
	public $limitContentByEndUserLocation;
				
	/**
	 * @var boolean
	 **/
	public $limitToAccountSharing;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $limitToRoleIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToRoles;
				
	/**
	 * @var string
	 **/
	public $logoURL;
				
	/**
	 * @var string
	 **/
	public $mainSiteURL;
				
	/**
	 * @var long
	 **/
	public $maximumAPIRequestsPerDay;
				
	/**
	 * @var long
	 **/
	public $maximumEncodingProfileTotalBitrate;
				
	/**
	 * @var float
	 **/
	public $maximumPaymentPerTransaction;
				
	/**
	 * @var long
	 **/
	public $maximumReleaseRequestsPerDay;
				
	/**
	 * @var long
	 **/
	public $maximumUsageReportRequestsPerDay;
				
	/**
	 * @var ComcastEncoding
	 **/
	public $metafileEncoding;
				
	/**
	 * @var string
	 **/
	public $name;
				
	/**
	 * @var ComcastArrayOfNotificationAction
	 **/
	public $notificationActions;
				
	/**
	 * @var boolean
	 **/
	public $notificationIsPublic;
				
	/**
	 * @var ComcastArrayOfAPIObject
	 **/
	public $notificationItems;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $notificationNetworks;
				
	/**
	 * @var string
	 **/
	public $notificationPassword;
				
	/**
	 * @var string
	 **/
	public $notificationURL;
				
	/**
	 * @var string
	 **/
	public $notificationUserName;
				
	/**
	 * @var string
	 **/
	public $payPageURL;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $paymentFailureEmailAddresses;
				
	/**
	 * @var ComcastPaymentGateway
	 **/
	public $paymentGateway;
				
	/**
	 * @var string
	 **/
	public $paymentGatewayAccount;
				
	/**
	 * @var string
	 **/
	public $paymentGatewayPassword;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $permissionIDs;
				
	/**
	 * @var string
	 **/
	public $playerAdminServiceURL;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $possibleRatings;
				
	/**
	 * @var string
	 **/
	public $releaseURL;
				
	/**
	 * @var long
	 **/
	public $storageUsed;
				
	/**
	 * @var string
	 **/
	public $stylesheetURL;
				
	/**
	 * @var string
	 **/
	public $subdomain;
				
	/**
	 * @var ComcastArrayOfCountry
	 **/
	public $targetCountries;
				
	/**
	 * @var float
	 **/
	public $thumbnailAdjustment;
				
	/**
	 * @var string
	 **/
	public $thumbnailBackgroundColor;
				
	/**
	 * @var int
	 **/
	public $thumbnailWidth;
				
	/**
	 * @var boolean
	 **/
	public $trackBrowserByDefault;
				
	/**
	 * @var boolean
	 **/
	public $trackLocationByDefault;
				
	/**
	 * @var string
	 **/
	public $transcriptFooter;
				
	/**
	 * @var string
	 **/
	public $transcriptHeader;
				
	/**
	 * @var long
	 **/
	public $uploadQuota;
				
	/**
	 * @var boolean
	 **/
	public $useFLVServersForMPEG4;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerBannerHTML;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerErrorMessageBaseURL;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerFLVDownloadServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerFLVPushServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerFLVStorageServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerFLVStreamingServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerHomeTabHeaderHeight;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerLogoURL;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerMainSiteURL;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerOtherDownloadServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerOtherPushServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerOtherStorageServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerOtherStreamingServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerQTDownloadServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerQTPushServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerQTStorageServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerQTStreamingServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerRealDownloadServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerRealPushServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerRealStorageServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerRealStreamingServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerStylesheetURL;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerThumbnailServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerWMDownloadServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerWMPushServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerWMStorageServer;
				
	/**
	 * @var boolean
	 **/
	public $useOwnerWMStreamingServer;
				
	/**
	 * @var boolean
	 **/
	public $usePaymentGatewayTestMode;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $visibleToAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $visibleToAccounts;
				
	/**
	 * @var boolean
	 **/
	public $visibleToAllAccounts;
				
	/**
	 * @var string
	 **/
	public $wmrmLicenseAcquisitionURL;
				
	/**
	 * @var string
	 **/
	public $wmrmLicenseKeySeed;
				
	/**
	 * @var string
	 **/
	public $wmrmPrivateKey;
				
	/**
	 * @var string
	 **/
	public $wmrmPublicKey;
				
	/**
	 * @var string
	 **/
	public $wmrmRevocationPrivateKey;
				
	/**
	 * @var string
	 **/
	public $wmrmRevocationPublicKey;
				
	/**
	 * @var int
	 **/
	public $writeActionLimit;
				
}


