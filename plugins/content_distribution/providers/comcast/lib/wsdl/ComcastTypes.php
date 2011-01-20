<?php


class ComcastPermissionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPermission");	
	}
					
}
	
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
	
class ComcastRoleList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRole");	
	}
					
}
	
class ComcastRole extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRoleField';
			case 'capabilities':
				return 'ComcastArrayOfCapability';
			case 'externalGroups':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRoleField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $allowAPICalls;
				
	/**
	 * @var boolean
	 **/
	public $allowConsoleAccess;
				
	/**
	 * @var ComcastArrayOfCapability
	 **/
	public $capabilities;
				
	/**
	 * @var boolean
	 **/
	public $copyToNewAccounts;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $externalGroups;
				
	/**
	 * @var boolean
	 **/
	public $grantByDefault;
				
	/**
	 * @var boolean
	 **/
	public $grantFutureCapabilities;
				
	/**
	 * @var boolean
	 **/
	public $showHomeTab;
				
	/**
	 * @var string
	 **/
	public $title;
				
}
	
class ComcastCapability extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'APIObject':
				return 'ComcastAPIObject';
			case 'capabilityType':
				return 'ComcastCapabilityType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastAPIObject
	 **/
	public $APIObject;
				
	/**
	 * @var ComcastCapabilityType
	 **/
	public $capabilityType;
				
}
	
class ComcastArrayOfCapability extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCapability");	
	}
					
}
	
class ComcastUserList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUser");	
	}
					
}
	
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
	
class ComcastAccountList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAccount");	
	}
					
}
	
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
	
class ComcastCustomFieldList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomField");	
	}
					
}
	
class ComcastCustomField extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCustomFieldField';
			case 'allowedTextValues':
				return 'ComcastArrayOfstring';
			case 'fieldType':
				return 'ComcastCustomFieldType';
			case 'limitToAPIObjects':
				return 'ComcastArrayOfAPIObject';
			case 'shareWithAccountIDs':
				return 'ComcastIDSet';
			case 'shareWithAccounts':
				return 'ComcastArrayOfstring';
			case 'supportedFormats':
				return 'ComcastArrayOfFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCustomFieldField
	 **/
	public $template;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedTextValues;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var string
	 **/
	public $defaultTextValue;
				
	/**
	 * @var string
	 **/
	public $fieldName;
				
	/**
	 * @var ComcastCustomFieldType
	 **/
	public $fieldType;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var boolean
	 **/
	public $includeInMetafiles;
				
	/**
	 * @var boolean
	 **/
	public $includeInReleases;
				
	/**
	 * @var int
	 **/
	public $length;
				
	/**
	 * @var ComcastArrayOfAPIObject
	 **/
	public $limitToAPIObjects;
				
	/**
	 * @var int
	 **/
	public $linesToDisplay;
				
	/**
	 * @var string
	 **/
	public $namespace;
				
	/**
	 * @var string
	 **/
	public $namespacePrefix;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $shareWithAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $shareWithAccounts;
				
	/**
	 * @var boolean
	 **/
	public $shareWithAllAccounts;
				
	/**
	 * @var boolean
	 **/
	public $showInMoreFields;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $supportedFormats;
				
	/**
	 * @var string
	 **/
	public $title;
				
}
	
class ComcastLocationList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLocation");	
	}
					
}
	
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
	
class ComcastServerList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastServer");	
	}
					
}
	
class ComcastServer extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfServerField';
			case 'delivery':
				return 'ComcastDelivery';
			case 'dropFolderURLs':
				return 'ComcastArrayOfstring';
			case 'format':
				return 'ComcastFormat';
			case 'icon':
				return 'ComcastServerIcon';
			case 'mediaFileIDs':
				return 'ComcastIDSet';
			case 'releaseIDs':
				return 'ComcastIDSet';
			case 'storageNetworks':
				return 'ComcastArrayOfstring';
			case 'uploadBaseURLs':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfServerField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $availableForStorage;
				
	/**
	 * @var boolean
	 **/
	public $availableToChildAccountsByDefault;
				
	/**
	 * @var string
	 **/
	public $backupStreamingURL;
				
	/**
	 * @var boolean
	 **/
	public $custom;
				
	/**
	 * @var string
	 **/
	public $deleteURL;
				
	/**
	 * @var boolean
	 **/
	public $deliverFromStorageForHTTP;
				
	/**
	 * @var boolean
	 **/
	public $deliversMetafiles;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var float
	 **/
	public $deliveryPercentage;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var string
	 **/
	public $displayTitle;
				
	/**
	 * @var string
	 **/
	public $downloadURL;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $dropFolderURLs;
				
	/**
	 * @var boolean
	 **/
	public $enableFileListURL;
				
	/**
	 * @var string
	 **/
	public $fileListOptions;
				
	/**
	 * @var string
	 **/
	public $fileListPassword;
				
	/**
	 * @var string
	 **/
	public $fileListURL;
				
	/**
	 * @var string
	 **/
	public $fileListUserName;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var ComcastServerIcon
	 **/
	public $icon;
				
	/**
	 * @var boolean
	 **/
	public $inUse;
				
	/**
	 * @var boolean
	 **/
	public $isPublic;
				
	/**
	 * @var long
	 **/
	public $maximumFolderCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForManyFiles;
				
	/**
	 * @var boolean
	 **/
	public $organizeFilesByOwner;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var string
	 **/
	public $pid;
				
	/**
	 * @var string
	 **/
	public $privateKey;
				
	/**
	 * @var boolean
	 **/
	public $promptsToDownload;
				
	/**
	 * @var string
	 **/
	public $publishingPassword;
				
	/**
	 * @var string
	 **/
	public $publishingURL;
				
	/**
	 * @var string
	 **/
	public $publishingUserName;
				
	/**
	 * @var string
	 **/
	public $pullURL;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseIDs;
				
	/**
	 * @var boolean
	 **/
	public $requireActiveFTP;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $storageNetworks;
				
	/**
	 * @var long
	 **/
	public $storageQuota;
				
	/**
	 * @var string
	 **/
	public $storageURL;
				
	/**
	 * @var long
	 **/
	public $storageUsed;
				
	/**
	 * @var string
	 **/
	public $streamingURL;
				
	/**
	 * @var boolean
	 **/
	public $supportsPush;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $updateFileLayout;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $uploadBaseURLs;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}
	
class ComcastSystemTaskList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemTask");	
	}
					
}
	
class ComcastSystemTask extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemTaskField';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'diagnostics':
				return 'ComcastArrayOfstring';
			case 'taskType':
				return 'ComcastTaskType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemTaskField
	 **/
	public $template;
				
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
	public $contentOwnerAccountId;
				
	/**
	 * @var string
	 **/
	public $contentTitle;
				
	/**
	 * @var string
	 **/
	public $destination;
				
	/**
	 * @var string
	 **/
	public $destinationLocation;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $diagnostics;
				
	/**
	 * @var int
	 **/
	public $failedAttempts;
				
	/**
	 * @var string
	 **/
	public $item;
				
	/**
	 * @var string
	 **/
	public $job;
				
	/**
	 * @var long
	 **/
	public $jobID;
				
	/**
	 * @var int
	 **/
	public $percentComplete;
				
	/**
	 * @var boolean
	 **/
	public $refresh;
				
	/**
	 * @var string
	 **/
	public $requiredServiceToken;
				
	/**
	 * @var string
	 **/
	public $serviceToken;
				
	/**
	 * @var string
	 **/
	public $source;
				
	/**
	 * @var string
	 **/
	public $sourceLocation;
				
	/**
	 * @var ComcastTaskType
	 **/
	public $taskType;
				
}
	
class ComcastCustomCommandList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommand");	
	}
					
}
	
class ComcastCustomCommand extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCustomCommandField';
			case 'requiredCapabilityTypes':
				return 'ComcastArrayOfCapabilityType';
			case 'views':
				return 'ComcastArrayOfAdminView';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCustomCommandField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var string
	 **/
	public $URLPassword;
				
	/**
	 * @var string
	 **/
	public $URLUserName;
				
	/**
	 * @var string
	 **/
	public $confirmationAlert;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var long
	 **/
	public $index;
				
	/**
	 * @var int
	 **/
	public $maximumItems;
				
	/**
	 * @var string
	 **/
	public $maximumItemsAlert;
				
	/**
	 * @var int
	 **/
	public $minimumItems;
				
	/**
	 * @var string
	 **/
	public $minimumItemsAlert;
				
	/**
	 * @var boolean
	 **/
	public $onlyForOwnedItems;
				
	/**
	 * @var string
	 **/
	public $onlyForOwnedItemsAlert;
				
	/**
	 * @var boolean
	 **/
	public $openInNewWindow;
				
	/**
	 * @var ComcastArrayOfCapabilityType
	 **/
	public $requiredCapabilityTypes;
				
	/**
	 * @var boolean
	 **/
	public $showAsDialog;
				
	/**
	 * @var boolean
	 **/
	public $showScrollbars;
				
	/**
	 * @var boolean
	 **/
	public $showToReadOnlyUsers;
				
	/**
	 * @var boolean
	 **/
	public $showToStandardUsers;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $useSelection;
				
	/**
	 * @var ComcastArrayOfAdminView
	 **/
	public $views;
				
	/**
	 * @var int
	 **/
	public $windowHeight;
				
	/**
	 * @var string
	 **/
	public $windowName;
				
	/**
	 * @var int
	 **/
	public $windowWidth;
				
}
	
class ComcastDirectoryList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDirectory");	
	}
					
}
	
class ComcastDirectory extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfDirectoryField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfDirectoryField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var boolean
	 **/
	public $grantAccessIfUnavailable;
				
	/**
	 * @var string
	 **/
	public $host;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var int
	 **/
	public $port;
				
	/**
	 * @var long
	 **/
	public $priority;
				
	/**
	 * @var string
	 **/
	public $scope;
				
	/**
	 * @var string
	 **/
	public $searchPattern;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $useSSL;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}
	
class ComcastJobList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastJob");	
	}
					
}
	
class ComcastJob extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfJobField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfJobField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyDelete;
				
	/**
	 * @var boolean
	 **/
	public $hasFailedTasks;
				
	/**
	 * @var boolean
	 **/
	public $processInOrder;
				
	/**
	 * @var boolean
	 **/
	public $ready;
				
	/**
	 * @var int
	 **/
	public $tasksRemaining;
				
	/**
	 * @var string
	 **/
	public $title;
				
}
	
class ComcastSystemStatus extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemStatusField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemStatusField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $buildDate;
				
	/**
	 * @var dateTime
	 **/
	public $currentDate;
				
	/**
	 * @var long
	 **/
	public $queuedConnections;
				
	/**
	 * @var string
	 **/
	public $rootAccount;
				
	/**
	 * @var long
	 **/
	public $rootAccountID;
				
	/**
	 * @var string
	 **/
	public $serverAddress;
				
	/**
	 * @var string
	 **/
	public $serverName;
				
	/**
	 * @var string
	 **/
	public $softwareVersion;
				
	/**
	 * @var dateTime
	 **/
	public $startDate;
				
	/**
	 * @var long
	 **/
	public $upTime;
				
	/**
	 * @var string
	 **/
	public $upTimeWithUnits;
				
	/**
	 * @var float
	 **/
	public $usageTrackingLoad;
				
	/**
	 * @var string
	 **/
	public $webXML;
				
}
	
class ComcastSystemRequestLog extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemRequestLogField';
			case 'failedAverageResponseTimes':
				return 'ComcastArrayOflong';
			case 'failedRequestCounts':
				return 'ComcastArrayOflong';
			case 'failureRates':
				return 'ComcastArrayOffloat';
			case 'requestCounts':
				return 'ComcastArrayOflong';
			case 'successfulAverageResponseTimes':
				return 'ComcastArrayOflong';
			case 'systemRequestType':
				return 'ComcastSystemRequestType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemRequestLogField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $currentDate;
				
	/**
	 * @var long
	 **/
	public $failedAverageResponseTime;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $failedAverageResponseTimes;
				
	/**
	 * @var long
	 **/
	public $failedRequestCount;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $failedRequestCounts;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerHour;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerMinute;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerSecond;
				
	/**
	 * @var float
	 **/
	public $failureRate;
				
	/**
	 * @var ComcastArrayOffloat
	 **/
	public $failureRates;
				
	/**
	 * @var long
	 **/
	public $requestCount;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $requestCounts;
				
	/**
	 * @var float
	 **/
	public $requestsPerHour;
				
	/**
	 * @var float
	 **/
	public $requestsPerMinute;
				
	/**
	 * @var float
	 **/
	public $requestsPerSecond;
				
	/**
	 * @var dateTime
	 **/
	public $sampleEndDate;
				
	/**
	 * @var long
	 **/
	public $sampleLength;
				
	/**
	 * @var dateTime
	 **/
	public $sampleStartDate;
				
	/**
	 * @var string
	 **/
	public $serverAddress;
				
	/**
	 * @var string
	 **/
	public $serverName;
				
	/**
	 * @var long
	 **/
	public $successfulAverageResponseTime;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $successfulAverageResponseTimes;
				
	/**
	 * @var ComcastSystemRequestType
	 **/
	public $systemRequestType;
				
	/**
	 * @var long
	 **/
	public $totalFailedRequestCount;
				
	/**
	 * @var long
	 **/
	public $totalRequestCount;
				
	/**
	 * @var long
	 **/
	public $totalSuccessfulRequestCount;
				
}
	
class ComcastIDSet extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
}
	
class ComcastCustomData extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomDataElement");	
	}
					
}
	
class ComcastBusinessObject extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'customData':
				return 'ComcastCustomData';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var long
	 **/
	public $ID;
				
	/**
	 * @var dateTime
	 **/
	public $added;
				
	/**
	 * @var string
	 **/
	public $addedByUser;
				
	/**
	 * @var string
	 **/
	public $addedByUserEmailAddress;
				
	/**
	 * @var long
	 **/
	public $addedByUserID;
				
	/**
	 * @var string
	 **/
	public $addedByUserName;
				
	/**
	 * @var string
	 **/
	public $description;
				
	/**
	 * @var dateTime
	 **/
	public $lastModified;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUser;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUserEmailAddress;
				
	/**
	 * @var long
	 **/
	public $lastModifiedByUserID;
				
	/**
	 * @var string
	 **/
	public $lastModifiedByUserName;
				
	/**
	 * @var boolean
	 **/
	public $locked;
				
	/**
	 * @var string
	 **/
	public $owner;
				
	/**
	 * @var long
	 **/
	public $ownerAccountID;
				
	/**
	 * @var int
	 **/
	public $version;
				
	/**
	 * @var ComcastCustomData
	 **/
	public $customData;
				
}
	
class ComcastStatusObject extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'status':
				return 'ComcastStatus';
			case 'statusDetail':
				return 'ComcastStatusDetail';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var boolean
	 **/
	public $refreshStatus;
				
	/**
	 * @var ComcastStatus
	 **/
	public $status;
				
	/**
	 * @var string
	 **/
	public $statusDescription;
				
	/**
	 * @var ComcastStatusDetail
	 **/
	public $statusDetail;
				
	/**
	 * @var string
	 **/
	public $statusMessage;
				
}
	
class ComcastCustomDataElement extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'value':
				return 'ComcastFieldValue';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var ComcastFieldValue
	 **/
	public $value;
				
}
	
class ComcastArrayOfCustomDataElement extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomDataElement");	
	}
					
}
	
class ComcastFieldValue extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastBooleanValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var boolean
	 **/
	public $value;
				
}
	
class ComcastHTMLValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $text;
				
}
	
class ComcastHyperlinkValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $altText;
				
	/**
	 * @var string
	 **/
	public $hyperlinkURL;
				
	/**
	 * @var string
	 **/
	public $mimeType;
				
	/**
	 * @var string
	 **/
	public $target;
				
}
	
class ComcastImageValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $altText;
				
	/**
	 * @var string
	 **/
	public $hyperlinkURL;
				
	/**
	 * @var string
	 **/
	public $imageURL;
				
	/**
	 * @var string
	 **/
	public $mimeType;
				
	/**
	 * @var string
	 **/
	public $target;
				
}
	
class ComcastLargeTextValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $text;
				
}
	
class ComcastTextValue extends ComcastFieldValue
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $text;
				
}
	
class ComcastQuery extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'parameterNames':
				return 'ComcastArrayOfstring';
			case 'parameterValues':
				return 'ComcastArrayOfanyType';
			case 'and':
				return 'ComcastArrayOfQuery';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $name;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $parameterNames;
				
	/**
	 * @var ComcastArrayOfanyType
	 **/
	public $parameterValues;
				
	/**
	 * @var ComcastArrayOfQuery
	 **/
	public $and;
				
}
	
class ComcastArrayOfQuery extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastQuery");	
	}
					
}
	
class ComcastRange extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var long
	 **/
	public $startIndex;
				
	/**
	 * @var long
	 **/
	public $endIndex;
				
}
	
class ComcastIDList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
}
	
class ComcastArrayOfCountry extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCountry");	
	}
					
}
	
class ComcastArrayOfAPIObject extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAPIObject");	
	}
					
}
	
class ComcastArrayOfAdminView extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAdminView");	
	}
					
}
	
class ComcastDigest extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $userName;
				
	/**
	 * @var string
	 **/
	public $realm;
				
	/**
	 * @var string
	 **/
	public $nonce;
				
	/**
	 * @var string
	 **/
	public $nc;
				
	/**
	 * @var string
	 **/
	public $cnonce;
				
	/**
	 * @var string
	 **/
	public $qop;
				
	/**
	 * @var string
	 **/
	public $method;
				
	/**
	 * @var string
	 **/
	public $uri;
				
	/**
	 * @var string
	 **/
	public $response;
				
	/**
	 * @var string
	 **/
	public $digestAlgorithm;
				
}
	
class ComcastKeySettings extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $userName;
				
	/**
	 * @var string
	 **/
	public $prefix;
				
	/**
	 * @var string
	 **/
	public $digestAlgorithm;
				
	/**
	 * @var string
	 **/
	public $key;
				
	/**
	 * @var boolean
	 **/
	public $useHexKey;
				
}
	
class ComcastJobHeader extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $job;
				
}
	
class ComcastFieldState extends SoapObject
{				
	const _U = 'U';
					
	const _P = 'P';
					
	const _R = 'R';
					
	const _W = 'W';
					
	const _RW = 'RW';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAPIObject extends SoapObject
{				
	const _ACCOUNT = 'Account';
					
	const _ASSETTYPE = 'AssetType';
					
	const _CATEGORY = 'Category';
					
	const _CHOICE = 'Choice';
					
	const _CUSTOMCOMMAND = 'CustomCommand';
					
	const _CUSTOMFIELD = 'CustomField';
					
	const _DIRECTORY = 'Directory';
					
	const _ENCODINGPROFILE = 'EncodingProfile';
					
	const _ENDUSER = 'EndUser';
					
	const _ENDUSERPERMISSION = 'EndUserPermission';
					
	const _ENDUSERTRANSACTION = 'EndUserTransaction';
					
	const _JOB = 'Job';
					
	const _LICENSE = 'License';
					
	const _LOCATION = 'Location';
					
	const _MEDIA = 'Media';
					
	const _MEDIAFILE = 'MediaFile';
					
	const _PERMISSION = 'Permission';
					
	const _PLAYLIST = 'Playlist';
					
	const _PORTAL = 'Portal';
					
	const _PRICE = 'Price';
					
	const _RELEASE = 'Release';
					
	const _REQUEST = 'Request';
					
	const _RESTRICTION = 'Restriction';
					
	const _ROLE = 'Role';
					
	const _SERVER = 'Server';
					
	const _STOREFRONT = 'Storefront';
					
	const _STOREFRONTPAGE = 'StorefrontPage';
					
	const _SYSTEMREQUESTLOG = 'SystemRequestLog';
					
	const _SYSTEMSTATUS = 'SystemStatus';
					
	const _SYSTEMTASK = 'SystemTask';
					
	const _USAGEPLAN = 'UsagePlan';
					
	const _USER = 'User';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAuthorizationMethod extends SoapObject
{				
	const _DIRECTORY = 'Directory';
					
	const _REMOTEUSER = 'RemoteUser';
					
	const _STOREDPASSWORD = 'StoredPassword';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastTimeZone extends SoapObject
{				
	const _LINEISLANDS = 'LineIslands';
					
	const _WESTSAMOA = 'WestSamoa';
					
	const _HAWAII = 'Hawaii';
					
	const _ALASKA = 'Alaska';
					
	const _PACIFICUSCANADA = 'PacificUSCanada';
					
	const _ARIZONA = 'Arizona';
					
	const _MAZATLAN = 'Mazatlan';
					
	const _MOUNTAINUSCANADA = 'MountainUSCanada';
					
	const _CENTRALAMERICA = 'CentralAmerica';
					
	const _CENTRALUSCANADA = 'CentralUSCanada';
					
	const _MEXICOCITY = 'MexicoCity';
					
	const _SASKATCHEWAN = 'Saskatchewan';
					
	const _COLUMBIA = 'Columbia';
					
	const _EASTERNUSCANADA = 'EasternUSCanada';
					
	const _INDIANAEAST = 'IndianaEast';
					
	const _VENEZUELA = 'Venezuela';
					
	const _ATLANTICCANADA = 'AtlanticCanada';
					
	const _CHILE = 'Chile';
					
	const _NEWFOUNDLAND = 'Newfoundland';
					
	const _BRASILIA = 'Brasilia';
					
	const _ARGENTINE = 'Argentine';
					
	const _WESTERNGREENLAND = 'WesternGreenland';
					
	const _SOUTHGEORGIA = 'SouthGeorgia';
					
	const _AZORES = 'Azores';
					
	const _CAPEVERDE = 'CapeVerde';
					
	const _WESTERNEUROPEAN = 'WesternEuropean';
					
	const _GREENWICHMEAN = 'GreenwichMean';
					
	const _CENTRALEUROPEANBERLIN = 'CentralEuropeanBerlin';
					
	const _CENTRALEUROPEANPRAGUE = 'CentralEuropeanPrague';
					
	const _CENTRALEUROPEANPARIS = 'CentralEuropeanParis';
					
	const _CENTRALEUROPEANWARSAW = 'CentralEuropeanWarsaw';
					
	const _WESTAFRICAN = 'WestAfrican';
					
	const _EASTERNEUROPEANATHENS = 'EasternEuropeanAthens';
					
	const _EASTERNEUROPEANBUCHAREST = 'EasternEuropeanBucharest';
					
	const _EASTERNEUROPEANCAIRO = 'EasternEuropeanCairo';
					
	const _CENTRALAFRICAN = 'CentralAfrican';
					
	const _EASTERNEUROPEANHELSINKI = 'EasternEuropeanHelsinki';
					
	const _ISRAEL = 'Israel';
					
	const _ARABIABAGHDAD = 'ArabiaBaghdad';
					
	const _ARABIARIYADH = 'ArabiaRiyadh';
					
	const _MOSCOW = 'Moscow';
					
	const _EASTERNAFRICAN = 'EasternAfrican';
					
	const _IRAN = 'Iran';
					
	const _GULF = 'Gulf';
					
	const _AZERBAIJAN = 'Azerbaijan';
					
	const _AFGHANISTAN = 'Afghanistan';
					
	const _EKATERINBURG = 'Ekaterinburg';
					
	const _PAKISTAN = 'Pakistan';
					
	const _INDIA = 'India';
					
	const _NEPAL = 'Nepal';
					
	const _NOVOSIBIRSK = 'Novosibirsk';
					
	const _BANGLADESH = 'Bangladesh';
					
	const _SRILANKA = 'SriLanka';
					
	const _MYANMAR = 'Myanmar';
					
	const _INDOCHINA = 'Indochina';
					
	const _KRASNOYARSK = 'Krasnoyarsk';
					
	const _HONGKONG = 'HongKong';
					
	const _ULAANBATAAR = 'UlaanBataar';
					
	const _MALAYSIA = 'Malaysia';
					
	const _WESTERNAUSTRALIA = 'WesternAustralia';
					
	const _CHINA = 'China';
					
	const _JAPAN = 'Japan';
					
	const _KOREA = 'Korea';
					
	const _YAKUTSK = 'Yakutsk';
					
	const _CENTRALSOUTHAUSTRALIA = 'CentralSouthAustralia';
					
	const _CENTRALNORTHERNTERRITORYAUSTRALIA = 'CentralNorthernTerritoryAustralia';
					
	const _EASTERNQUEENSLANDAUSTRALIA = 'EasternQueenslandAustralia';
					
	const _EASTERNNEWSOUTHWALESAUSTRALIA = 'EasternNewSouthWalesAustralia';
					
	const _CHAMORRO = 'Chamorro';
					
	const _EASTERNHOBARTAUSTRALIA = 'EasternHobartAustralia';
					
	const _VLADIVOSTOK = 'Vladivostok';
					
	const _SOLOMONISLANDS = 'SolomonIslands';
					
	const _NEWZEALAND = 'NewZealand';
					
	const _FIJI = 'Fiji';
					
	const _TONGA = 'Tonga';
					
	const _KIRITIMATI = 'Kiritimati';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastTimeUnits extends SoapObject
{				
	const _MINUTES = 'minutes';
					
	const _HOURS = 'hours';
					
	const _DAYS = 'days';
					
	const _WEEKS = 'weeks';
					
	const _MONTHS = 'months';
					
	const _YEARS = 'years';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastLanguage extends SoapObject
{				
	const _CUNKNOWND = 'CUnknownD';
					
	const _AFRIKAANS = 'Afrikaans';
					
	const _ALBANIAN = 'Albanian';
					
	const _AMHARIC = 'Amharic';
					
	const _ARABIC = 'Arabic';
					
	const _ARMENIAN = 'Armenian';
					
	const _ASSAMESE = 'Assamese';
					
	const _AZERI = 'Azeri';
					
	const _BASQUE = 'Basque';
					
	const _BELARUSIAN = 'Belarusian';
					
	const _BENGALI = 'Bengali';
					
	const _BULGARIAN = 'Bulgarian';
					
	const _BURMESE = 'Burmese';
					
	const _CANTONESE = 'Cantonese';
					
	const _CATALAN = 'Catalan';
					
	const _CHEROKEE = 'Cherokee';
					
	const _CHINESE = 'Chinese';
					
	const _CROATIAN = 'Croatian';
					
	const _CZECH = 'Czech';
					
	const _DANISH = 'Danish';
					
	const _DIVEHI = 'Divehi';
					
	const _DUTCH = 'Dutch';
					
	const _DZONGKHA = 'Dzongkha';
					
	const _EDO = 'Edo';
					
	const _ENGLISH = 'English';
					
	const _ESTONIAN = 'Estonian';
					
	const _FYRO_MACEDONIAN = 'FYRO Macedonian';
					
	const _FAEROESE = 'Faeroese';
					
	const _FARSI = 'Farsi';
					
	const _FILIPINO = 'Filipino';
					
	const _FINNISH = 'Finnish';
					
	const _FRENCH = 'French';
					
	const _FRISIAN = 'Frisian';
					
	const _FULFULDE = 'Fulfulde';
					
	const _GAELIC = 'Gaelic';
					
	const _GALICIAN = 'Galician';
					
	const _GEORGIAN = 'Georgian';
					
	const _GERMAN = 'German';
					
	const _GREEK = 'Greek';
					
	const _GUARANI = 'Guarani';
					
	const _GUJARATI = 'Gujarati';
					
	const _HAUSA = 'Hausa';
					
	const _HAWAIIAN = 'Hawaiian';
					
	const _HEBREW = 'Hebrew';
					
	const _HINDI = 'Hindi';
					
	const _HUNGARIAN = 'Hungarian';
					
	const _IBIBIO = 'Ibibio';
					
	const _ICELANDIC = 'Icelandic';
					
	const _IGBO = 'Igbo';
					
	const _INDONESIAN = 'Indonesian';
					
	const _INUKTITUT = 'Inuktitut';
					
	const _INUPIAK = 'Inupiak';
					
	const _ITALIAN = 'Italian';
					
	const _JAPANESE = 'Japanese';
					
	const _KANNADA = 'Kannada';
					
	const _KANURI = 'Kanuri';
					
	const _KASHMIRI = 'Kashmiri';
					
	const _KAZAKH = 'Kazakh';
					
	const _KHMER = 'Khmer';
					
	const _KONKANI = 'Konkani';
					
	const _KOREAN = 'Korean';
					
	const _KYRGYZ = 'Kyrgyz';
					
	const _LAO = 'Lao';
					
	const _LATIN = 'Latin';
					
	const _LATVIAN = 'Latvian';
					
	const _LITHUANIAN = 'Lithuanian';
					
	const _MALAGASY = 'Malagasy';
					
	const _MALAY = 'Malay';
					
	const _MALAYALAM = 'Malayalam';
					
	const _MALTESE = 'Maltese';
					
	const _MANDARIN = 'Mandarin';
					
	const _MANIPURI = 'Manipuri';
					
	const _MARATHI = 'Marathi';
					
	const _MONGOLIAN = 'Mongolian';
					
	const _NEPALI = 'Nepali';
					
	const _NORWEGIAN = 'Norwegian';
					
	const _ORIYA = 'Oriya';
					
	const _OROMO = 'Oromo';
					
	const _PASHTO = 'Pashto';
					
	const _POLISH = 'Polish';
					
	const _PORTUGUESE = 'Portuguese';
					
	const _PUNJABI = 'Punjabi';
					
	const _QUECHUA = 'Quechua';
					
	const _RHAETO_ROMANIC = 'Rhaeto-Romanic';
					
	const _ROMANIAN = 'Romanian';
					
	const _RUSSIAN = 'Russian';
					
	const _SAMI = 'Sami';
					
	const _SANSKRIT = 'Sanskrit';
					
	const _SERBIAN = 'Serbian';
					
	const _SINDHI = 'Sindhi';
					
	const _SINHALESE = 'Sinhalese';
					
	const _SLOVAK = 'Slovak';
					
	const _SLOVENIAN = 'Slovenian';
					
	const _SOMALI = 'Somali';
					
	const _SORBIAN = 'Sorbian';
					
	const _SPANISH = 'Spanish';
					
	const _SUTU = 'Sutu';
					
	const _SWAHILI = 'Swahili';
					
	const _SWEDISH = 'Swedish';
					
	const _SYRIAC = 'Syriac';
					
	const _TAJIK = 'Tajik';
					
	const _TAMAZIGHT = 'Tamazight';
					
	const _TAMIL = 'Tamil';
					
	const _TATAR = 'Tatar';
					
	const _TELUGU = 'Telugu';
					
	const _THAI = 'Thai';
					
	const _TIBETAN = 'Tibetan';
					
	const _TIGRIGNA = 'Tigrigna';
					
	const _TSONGA = 'Tsonga';
					
	const _TSWANA = 'Tswana';
					
	const _TURKISH = 'Turkish';
					
	const _TURKMEN = 'Turkmen';
					
	const _UKRAINIAN = 'Ukrainian';
					
	const _URDU = 'Urdu';
					
	const _UZBEK = 'Uzbek';
					
	const _VENDA = 'Venda';
					
	const _VIETNAMESE = 'Vietnamese';
					
	const _WELSH = 'Welsh';
					
	const _XHOSA = 'Xhosa';
					
	const _YI = 'Yi';
					
	const _YIDDISH = 'Yiddish';
					
	const _YORUBA = 'Yoruba';
					
	const _ZULU = 'Zulu';
					
	const _COTHERD = 'COtherD';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStatus extends SoapObject
{				
	const _ERROR = 'Error';
					
	const _INPROGRESS = 'InProgress';
					
	const _RETAINED = 'Retained';
					
	const _UNAPPROVED = 'Unapproved';
					
	const _DISABLED = 'Disabled';
					
	const _WARNING = 'Warning';
					
	const _OK = 'OK';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStatusDetail extends SoapObject
{				
	const _APPLYINGDRM = 'ApplyingDRM';
					
	const _AUTHENTICATIONERROR = 'AuthenticationError';
					
	const _CANNOTDELIVERINPLAYLIST = 'CannotDeliverInPlaylist';
					
	const _CANNOTREFUND = 'CannotRefund';
					
	const _CANNOTSHARE = 'CannotShare';
					
	const _CATEGORYUPDATEERROR = 'CategoryUpdateError';
					
	const _CONTAINERERROR = 'ContainerError';
					
	const _DELETEDCATEGORY = 'DeletedCategory';
					
	const _DELETEDCONTENT = 'DeletedContent';
					
	const _DELETEDENDUSER = 'DeletedEndUser';
					
	const _DELETEDOWNER = 'DeletedOwner';
					
	const _DELETEDSERVER = 'DeletedServer';
					
	const _DISABLED = 'Disabled';
					
	const _DRMERROR = 'DRMError';
					
	const _DRMUNSUPPORTEDCODEC = 'DRMUnsupportedCodec';
					
	const _ENCODINGERROR = 'EncodingError';
					
	const _ENCODINGFILE = 'EncodingFile';
					
	const _EXPIRED = 'Expired';
					
	const _GENERATINGTHUMBNAIL = 'GeneratingThumbnail';
					
	const _INTRANSIT = 'InTransit';
					
	const _MISSINGBILLINGADDRESS = 'MissingBillingAddress';
					
	const _MISSINGCREDITCARD = 'MissingCreditCard';
					
	const _MISSINGVIDEO = 'MissingVideo';
					
	const _NOCHILDREN = 'NoChildren';
					
	const _NOCOMMONFORMATS = 'NoCommonFormats';
					
	const _NODELIVERY = 'NoDelivery';
					
	const _NONE = 'None';
					
	const _NOTAVAILABLE = 'NotAvailable';
					
	const _NOTFOUND = 'NotFound';
					
	const _NOTIFICATIONERROR = 'NotificationError';
					
	const _NOTPAIDINFULL = 'NotPaidInFull';
					
	const _OVERAMOUNTBILLABLE = 'OverAmountBillable';
					
	const _OVERMAXIMUMBITRATE = 'OverMaximumBitrate';
					
	const _PROCESSINGCREDITCARD = 'ProcessingCreditCard';
					
	const _PROCESSINGERROR = 'ProcessingError';
					
	const _PROTECTEDFILE = 'ProtectedFile';
					
	const _PUBLISHINGWEBSERVICE = 'PublishingWebService';
					
	const _PUBLISHINGWEBSERVICEERROR = 'PublishingWebServiceError';
					
	const _REQUIRESDRM = 'RequiresDRM';
					
	const _ROLLEDUPFROMCHOICE = 'RolledUpFromChoice';
					
	const _ROLLEDUPFROMCONTENT = 'RolledUpFromContent';
					
	const _ROLLEDUPFROMENDUSER = 'RolledUpFromEndUser';
					
	const _ROLLEDUPFROMENDUSERPERMISSION = 'RolledUpFromEndUserPermission';
					
	const _ROLLEDUPFROMENDUSERTRANSACTION = 'RolledUpFromEndUserTransaction';
					
	const _ROLLEDUPFROMLOCATION = 'RolledUpFromLocation';
					
	const _ROLLEDUPFROMMEDIAFILE = 'RolledUpFromMediaFile';
					
	const _ROLLEDUPFROMRELEASE = 'RolledUpFromRelease';
					
	const _ROLLEDUPFROMSERVER = 'RolledUpFromServer';
					
	const _SERVERMISMATCH = 'ServerMismatch';
					
	const _THUMBNAILERROR = 'ThumbnailError';
					
	const _TRANSFERERROR = 'TransferError';
					
	const _UNAPPROVEDCONTENT = 'UnapprovedContent';
					
	const _UNAVAILABLEAUDIOCODEC = 'UnavailableAudioCodec';
					
	const _UNAVAILABLEAUDIOCODECSETTINGS = 'UnavailableAudioCodecSettings';
					
	const _UNAVAILABLECONTENT = 'UnavailableContent';
					
	const _UNAVAILABLEOWNER = 'UnavailableOwner';
					
	const _UNAVAILABLESERVER = 'UnavailableServer';
					
	const _UNAVAILABLESETTINGS = 'UnavailableSettings';
					
	const _UNAVAILABLEVIDEOCODEC = 'UnavailableVideoCodec';
					
	const _UNAVAILABLEVIDEOCODECSETTINGS = 'UnavailableVideoCodecSettings';
					
	const _UNKNOWN = 'Unknown';
					
	const _UNKNOWNFORMAT = 'UnknownFormat';
					
	const _UPDATINGCATEGORY = 'UpdatingCategory';
					
	const _VERIFICATIONERROR = 'VerificationError';
					
	const _VERIFYING = 'Verifying';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCountry extends SoapObject
{				
	const _CUNKNOWND = 'CUnknownD';
					
	const _UNITED_STATES = 'United States';
					
	const _CANADA = 'Canada';
					
	const _CGLOBALD = 'CGlobalD';
					
	const _AFGHANISTAN = 'Afghanistan';
					
	const _ALBANIA = 'Albania';
					
	const _ALGERIA = 'Algeria';
					
	const _AMERICAN_SAMOA = 'American Samoa';
					
	const _ANDORRA = 'Andorra';
					
	const _ANGOLA = 'Angola';
					
	const _ANGUILLA = 'Anguilla';
					
	const _ANTARCTICA = 'Antarctica';
					
	const _ANTIGUA_AND_BARBUDA = 'Antigua and Barbuda';
					
	const _ARGENTINA = 'Argentina';
					
	const _ARMENIA = 'Armenia';
					
	const _ARUBA = 'Aruba';
					
	const _AUSTRALIA = 'Australia';
					
	const _AUSTRIA = 'Austria';
					
	const _AZERBAIJAN = 'Azerbaijan';
					
	const _BAHAMAS = 'Bahamas';
					
	const _BAHRAIN = 'Bahrain';
					
	const _BANGLADESH = 'Bangladesh';
					
	const _BARBADOS = 'Barbados';
					
	const _BELARUS = 'Belarus';
					
	const _BELGIUM = 'Belgium';
					
	const _BELIZE = 'Belize';
					
	const _BENIN = 'Benin';
					
	const _BERMUDA = 'Bermuda';
					
	const _BHUTAN = 'Bhutan';
					
	const _BOLIVIA = 'Bolivia';
					
	const _BOSNIA_AND_HERZEGOVINA = 'Bosnia and Herzegovina';
					
	const _BOTSWANA = 'Botswana';
					
	const _BRAZIL = 'Brazil';
					
	const _BRITISH_INDIAN_OCEAN_TERRITORY = 'British Indian Ocean Territory';
					
	const _BRITISH_VIRGIN_ISLANDS = 'British Virgin Islands';
					
	const _BRUNEI = 'Brunei';
					
	const _BULGARIA = 'Bulgaria';
					
	const _BURKINA_FASO = 'Burkina Faso';
					
	const _BURUNDI = 'Burundi';
					
	const _CAMBODIA = 'Cambodia';
					
	const _CAMEROON = 'Cameroon';
					
	const _CAPE_VERDE = 'Cape Verde';
					
	const _CAYMAN_ISLANDS = 'Cayman Islands';
					
	const _CENTRAL_AFRICAN_REPUBLIC = 'Central African Republic';
					
	const _CHAD = 'Chad';
					
	const _CHILE = 'Chile';
					
	const _CHINA = 'China';
					
	const _CHRISTMAS_ISLAND = 'Christmas Island';
					
	const _COCOS_CKEELINGD_ISLANDS = 'Cocos CKeelingD Islands';
					
	const _COLOMBIA = 'Colombia';
					
	const _COMOROS = 'Comoros';
					
	const _CONGO = 'Congo';
					
	const _CONGO_CDRCD = 'Congo CDRCD';
					
	const _COOK_ISLANDS = 'Cook Islands';
					
	const _COSTA_RICA = 'Costa Rica';
					
	const _CROATIA = 'Croatia';
					
	const _CUBA = 'Cuba';
					
	const _CYPRUS = 'Cyprus';
					
	const _CZECH_REPUBLIC = 'Czech Republic';
					
	const _CTE_DAIVOIRE = 'Cte dAIvoire';
					
	const _DENMARK = 'Denmark';
					
	const _DJIBOUTI = 'Djibouti';
					
	const _DOMINICA = 'Dominica';
					
	const _DOMINICAN_REPUBLIC = 'Dominican Republic';
					
	const _ECUADOR = 'Ecuador';
					
	const _EGYPT = 'Egypt';
					
	const _EL_SALVADOR = 'El Salvador';
					
	const _EQUATORIAL_GUINEA = 'Equatorial Guinea';
					
	const _ERITREA = 'Eritrea';
					
	const _ESTONIA = 'Estonia';
					
	const _ETHIOPIA = 'Ethiopia';
					
	const _EUROPEAN_UNION = 'European Union';
					
	const _FALKLAND_ISLANDS_CISLAS_MALVINASD = 'Falkland Islands CIslas MalvinasD';
					
	const _FAROE_ISLANDS = 'Faroe Islands';
					
	const _FIJI = 'Fiji';
					
	const _FINLAND = 'Finland';
					
	const _FRANCE = 'France';
					
	const _FRENCH_GUIANA = 'French Guiana';
					
	const _FRENCH_POLYNESIA = 'French Polynesia';
					
	const _FRENCH_SOUTHERN_TERRITORIES = 'French Southern Territories';
					
	const _GABON = 'Gabon';
					
	const _GAMBIA = 'Gambia';
					
	const _GEORGIA = 'Georgia';
					
	const _GERMANY = 'Germany';
					
	const _GHANA = 'Ghana';
					
	const _GIBRALTAR = 'Gibraltar';
					
	const _GREECE = 'Greece';
					
	const _GREENLAND = 'Greenland';
					
	const _GRENADA = 'Grenada';
					
	const _GUADELOUPE = 'Guadeloupe';
					
	const _GUAM = 'Guam';
					
	const _GUATEMALA = 'Guatemala';
					
	const _GUINEA = 'Guinea';
					
	const _GUINEA_BISSAU = 'Guinea-Bissau';
					
	const _GUYANA = 'Guyana';
					
	const _HAITI = 'Haiti';
					
	const _HONDURAS = 'Honduras';
					
	const _HONG_KONG = 'Hong Kong';
					
	const _HUNGARY = 'Hungary';
					
	const _ICELAND = 'Iceland';
					
	const _INDIA = 'India';
					
	const _INDONESIA = 'Indonesia';
					
	const _IRAN = 'Iran';
					
	const _IRAQ = 'Iraq';
					
	const _IRELAND = 'Ireland';
					
	const _ISRAEL = 'Israel';
					
	const _ITALY = 'Italy';
					
	const _JAMAICA = 'Jamaica';
					
	const _JAPAN = 'Japan';
					
	const _JORDAN = 'Jordan';
					
	const _KAZAKHSTAN = 'Kazakhstan';
					
	const _KENYA = 'Kenya';
					
	const _KIRIBATI = 'Kiribati';
					
	const _KUWAIT = 'Kuwait';
					
	const _KYRGYZSTAN = 'Kyrgyzstan';
					
	const _LAOS = 'Laos';
					
	const _LATVIA = 'Latvia';
					
	const _LEBANON = 'Lebanon';
					
	const _LESOTHO = 'Lesotho';
					
	const _LIBERIA = 'Liberia';
					
	const _LIBYA = 'Libya';
					
	const _LIECHTENSTEIN = 'Liechtenstein';
					
	const _LITHUANIA = 'Lithuania';
					
	const _LUXEMBOURG = 'Luxembourg';
					
	const _MACAU = 'Macau';
					
	const _MACEDONIA = 'Macedonia';
					
	const _MADAGASCAR = 'Madagascar';
					
	const _MALAWI = 'Malawi';
					
	const _MALAYSIA = 'Malaysia';
					
	const _MALDIVES = 'Maldives';
					
	const _MALI = 'Mali';
					
	const _MALTA = 'Malta';
					
	const _MARSHALL_ISLANDS = 'Marshall Islands';
					
	const _MARTINIQUE = 'Martinique';
					
	const _MAURITANIA = 'Mauritania';
					
	const _MAURITIUS = 'Mauritius';
					
	const _MAYOTTE = 'Mayotte';
					
	const _MEXICO = 'Mexico';
					
	const _MICRONESIA = 'Micronesia';
					
	const _MOLDOVA = 'Moldova';
					
	const _MONACO = 'Monaco';
					
	const _MONGOLIA = 'Mongolia';
					
	const _MONTSERRAT = 'Montserrat';
					
	const _MOROCCO = 'Morocco';
					
	const _MOZAMBIQUE = 'Mozambique';
					
	const _MYANMAR = 'Myanmar';
					
	const _NAMIBIA = 'Namibia';
					
	const _NAURU = 'Nauru';
					
	const _NEPAL = 'Nepal';
					
	const _NETHERLANDS = 'Netherlands';
					
	const _NETHERLANDS_ANTILLES = 'Netherlands Antilles';
					
	const _NEW_CALEDONIA = 'New Caledonia';
					
	const _NEW_ZEALAND = 'New Zealand';
					
	const _NICARAGUA = 'Nicaragua';
					
	const _NIGER = 'Niger';
					
	const _NIGERIA = 'Nigeria';
					
	const _NIUE = 'Niue';
					
	const _NORFOLK_ISLAND = 'Norfolk Island';
					
	const _NORTH_KOREA = 'North Korea';
					
	const _NORTHERN_MARIANA_ISLANDS = 'Northern Mariana Islands';
					
	const _NORWAY = 'Norway';
					
	const _OMAN = 'Oman';
					
	const _PAKISTAN = 'Pakistan';
					
	const _PALAU = 'Palau';
					
	const _PALESTINIAN_TERRITORY = 'Palestinian Territory';
					
	const _PANAMA = 'Panama';
					
	const _PAPUA_NEW_GUINEA = 'Papua New Guinea';
					
	const _PARAGUAY = 'Paraguay';
					
	const _PERU = 'Peru';
					
	const _PHILIPPINES = 'Philippines';
					
	const _PITCAIRN_ISLANDS = 'Pitcairn Islands';
					
	const _POLAND = 'Poland';
					
	const _PORTUGAL = 'Portugal';
					
	const _PUERTO_RICO = 'Puerto Rico';
					
	const _QATAR = 'Qatar';
					
	const _ROMANIA = 'Romania';
					
	const _RUSSIA = 'Russia';
					
	const _RWANDA = 'Rwanda';
					
	const _RUNION = 'Runion';
					
	const _SAINT_HELENA = 'Saint Helena';
					
	const _SAINT_KITTS_AND_NEVIS = 'Saint Kitts and Nevis';
					
	const _SAINT_LUCIA = 'Saint Lucia';
					
	const _SAINT_PIERRE_AND_MIQUELON = 'Saint Pierre and Miquelon';
					
	const _SAINT_VINCENT_AND_THE_GRENADINES = 'Saint Vincent and the Grenadines';
					
	const _SAMOA = 'Samoa';
					
	const _SAN_MARINO = 'San Marino';
					
	const _SAUDI_ARABIA = 'Saudi Arabia';
					
	const _SENEGAL = 'Senegal';
					
	const _SERBIA_AND_MONTENEGRO = 'Serbia and Montenegro';
					
	const _SEYCHELLES = 'Seychelles';
					
	const _SIERRA_LEONE = 'Sierra Leone';
					
	const _SINGAPORE = 'Singapore';
					
	const _SLOVAKIA = 'Slovakia';
					
	const _SLOVENIA = 'Slovenia';
					
	const _SOLOMON_ISLANDS = 'Solomon Islands';
					
	const _SOMALIA = 'Somalia';
					
	const _SOUTH_AFRICA = 'South Africa';
					
	const _SOUTH_GEORGIA_AND_THE_SOUTH_SANDWICH_ISLANDS = 'South Georgia and the South Sandwich Islands';
					
	const _SOUTH_KOREA = 'South Korea';
					
	const _SPAIN = 'Spain';
					
	const _SRI_LANKA = 'Sri Lanka';
					
	const _SUDAN = 'Sudan';
					
	const _SURINAME = 'Suriname';
					
	const _SVALBARD_AND_JAN_MAYEN = 'Svalbard and Jan Mayen';
					
	const _SWAZILAND = 'Swaziland';
					
	const _SWEDEN = 'Sweden';
					
	const _SWITZERLAND = 'Switzerland';
					
	const _SYRIA = 'Syria';
					
	const _SO_TOM_AND_PRNCIPE = 'So Tom and Prncipe';
					
	const _TAIWAN = 'Taiwan';
					
	const _TAJIKISTAN = 'Tajikistan';
					
	const _TANZANIA = 'Tanzania';
					
	const _THAILAND = 'Thailand';
					
	const _TIMOR_LESTE = 'Timor-Leste';
					
	const _TOGO = 'Togo';
					
	const _TOKELAU = 'Tokelau';
					
	const _TONGA = 'Tonga';
					
	const _TRINIDAD_AND_TOBAGO = 'Trinidad and Tobago';
					
	const _TUNISIA = 'Tunisia';
					
	const _TURKEY = 'Turkey';
					
	const _TURKMENISTAN = 'Turkmenistan';
					
	const _TURKS_AND_CAICOS_ISLANDS = 'Turks and Caicos Islands';
					
	const _TUVALU = 'Tuvalu';
					
	const _U_S__VIRGIN_ISLANDS = 'U.S. Virgin Islands';
					
	const _UGANDA = 'Uganda';
					
	const _UKRAINE = 'Ukraine';
					
	const _UNITED_ARAB_EMIRATES = 'United Arab Emirates';
					
	const _UNITED_KINGDOM = 'United Kingdom';
					
	const _UNITED_STATES_MINOR_OUTLYING_ISLANDS = 'United States Minor Outlying Islands';
					
	const _URUGUAY = 'Uruguay';
					
	const _UZBEKISTAN = 'Uzbekistan';
					
	const _VANUATU = 'Vanuatu';
					
	const _VATICAN = 'Vatican';
					
	const _VENEZUELA = 'Venezuela';
					
	const _VIETNAM = 'Vietnam';
					
	const _WALLIS_AND_FUTUNA = 'Wallis and Futuna';
					
	const _WESTERN_SAHARA = 'Western Sahara';
					
	const _YEMEN = 'Yemen';
					
	const _YUGOSLAVIA = 'Yugoslavia';
					
	const _ZAIRE = 'Zaire';
					
	const _ZAMBIA = 'Zambia';
					
	const _ZIMBABWE = 'Zimbabwe';
					
	const _COTHERD = 'COtherD';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEncoding extends SoapObject
{				
	const _ARABIC_CASMO_708D = 'Arabic CASMO 708D';
					
	const _ARABIC_CISOD = 'Arabic CISOD';
					
	const _BALTIC_CISOD = 'Baltic CISOD';
					
	const _BALTIC_CWINDOWSD = 'Baltic CWindowsD';
					
	const _CENTRAL_EUROPEAN_CDOSD = 'Central European CDOSD';
					
	const _CENTRAL_EUROPEAN_CISOD = 'Central European CISOD';
					
	const _CENTRAL_EUROPEAN_CWINDOWSD = 'Central European CWindowsD';
					
	const _CHINESE_SIMPLIFIED_CGB2312D = 'Chinese Simplified CGB2312D';
					
	const _CHINESE_TRADITIONAL = 'Chinese Traditional';
					
	const _CYRILLIC_CDOSD = 'Cyrillic CDOSD';
					
	const _CYRILLIC_CISOD = 'Cyrillic CISOD';
					
	const _CYRILLIC_CKOI8_RD = 'Cyrillic CKOI8-RD';
					
	const _CYRILLIC_CWINDOWSD = 'Cyrillic CWindowsD';
					
	const _GREEK_CISOD = 'Greek CISOD';
					
	const _HEBREW_CDOSD = 'Hebrew CDOSD';
					
	const _HEBREW_CISO_VISUALD = 'Hebrew CISO-VisualD';
					
	const _HEBREW_CWINDOWSD = 'Hebrew CWindowsD';
					
	const _JAPANESE_CEUCD = 'Japanese CEUCD';
					
	const _JAPANESE_CJISD = 'Japanese CJISD';
					
	const _JAPANESE_CJIS_ALLOW_1_BYTE_KANAD = 'Japanese CJIS-Allow 1 byte KanaD';
					
	const _JAPANESE_CSHIFT_JISD = 'Japanese CShift-JISD';
					
	const _KOREAN = 'Korean';
					
	const _LATIN_3_CISOD = 'Latin 3 CISOD';
					
	const _LATIN_9_CISOD = 'Latin 9 CISOD';
					
	const _THAI = 'Thai';
					
	const _TURKISH_CISOD = 'Turkish CISOD';
					
	const _TURKISH_CWINDOWSD = 'Turkish CWindowsD';
					
	const _US_ASCII = 'US-ASCII';
					
	const _UNICODE = 'Unicode';
					
	const _UNICODE_CBIG_ENDIAND = 'Unicode CBig-EndianD';
					
	const _UNICODE_CUTF_8D = 'Unicode CUTF-8D';
					
	const _VIETNAMESE = 'Vietnamese';
					
	const _WESTERN_EUROPEAN_CISOD = 'Western European CISOD';
					
	const _WESTERN_EUROPEAN_CWINDOWSD = 'Western European CWindowsD';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCustomFieldType extends SoapObject
{				
	const _BOOLEAN = 'Boolean';
					
	const _HTML = 'HTML';
					
	const _HYPERLINK = 'Hyperlink';
					
	const _IMAGE = 'Image';
					
	const _LARGE_TEXT = 'Large Text';
					
	const _TEXT = 'Text';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastTaskType extends SoapObject
{				
	const _ADDEXTERNALRELEASE = 'AddExternalRelease';
					
	const _COPYFILE = 'CopyFile';
					
	const _DELETEEXTERNALRELEASE = 'DeleteExternalRelease';
					
	const _DELETEFILE = 'DeleteFile';
					
	const _ENCODEFILE = 'EncodeFile';
					
	const _FETCHFILE = 'FetchFile';
					
	const _GENERATETHUMBNAIL = 'GenerateThumbnail';
					
	const _MOVEFILE = 'MoveFile';
					
	const _PROTECTFILE = 'ProtectFile';
					
	const _PUBLISHCONTENT = 'PublishContent';
					
	const _SENDDIAGNOSTICS = 'SendDiagnostics';
					
	const _SENDNOTIFICATION = 'SendNotification';
					
	const _SETEXTERNALRELEASE = 'SetExternalRelease';
					
	const _UPDATEFILELAYOUT = 'UpdateFileLayout';
					
	const _VERIFYFILE = 'VerifyFile';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastArrayOfstring extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("string");	
	}
					
}
	
class ComcastArrayOfanyType extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("anyType");	
	}
					
}
	
class ComcastPermissionTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfPermissionField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPermissionField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastRoleTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfRoleField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRoleField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastUserTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfUserField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfUserField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastAccountTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfAccountField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfAccountField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastCustomFieldTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfCustomFieldField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCustomFieldField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastLocationTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfLocationField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfLocationField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastServerTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfServerField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfServerField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastSystemTaskTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfSystemTaskField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemTaskField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastCustomCommandTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfCustomCommandField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCustomCommandField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastDirectoryTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfDirectoryField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfDirectoryField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastJobTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfJobField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfJobField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastSystemStatusTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfSystemStatusField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemStatusField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastSystemRequestLogTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfSystemRequestLogField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfSystemRequestLogField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastArrayOfPermissionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPermissionField");	
	}
					
}
	
class ComcastArrayOfRoleField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRoleField");	
	}
					
}
	
class ComcastArrayOfUserField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUserField");	
	}
					
}
	
class ComcastArrayOfFormat extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastFormat");	
	}
					
}
	
class ComcastArrayOfNotificationAction extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastNotificationAction");	
	}
					
}
	
class ComcastArrayOfAccountField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAccountField");	
	}
					
}
	
class ComcastArrayOfCustomFieldField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomFieldField");	
	}
					
}
	
class ComcastArrayOfLocationField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLocationField");	
	}
					
}
	
class ComcastArrayOfServerField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastServerField");	
	}
					
}
	
class ComcastArrayOfSystemTaskField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemTaskField");	
	}
					
}
	
class ComcastArrayOfCapabilityType extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCapabilityType");	
	}
					
}
	
class ComcastArrayOfCustomCommandField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommandField");	
	}
					
}
	
class ComcastArrayOfDirectoryField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDirectoryField");	
	}
					
}
	
class ComcastArrayOfJobField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastJobField");	
	}
					
}
	
class ComcastArrayOfSystemStatusField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemStatusField");	
	}
					
}
	
class ComcastArrayOfSystemRequestLogField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemRequestLogField");	
	}
					
}
	
class ComcastPermissionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACCOUNTID = 'accountID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLYTOSUBACCOUNTS = 'applyToSubAccounts';
					
	const _DESCRIPTION = 'description';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _ROLEIDS = 'roleIDs';
					
	const _ROLETITLES = 'roleTitles';
					
	const _SHOWHOMETAB = 'showHomeTab';
					
	const _USERADDED = 'userAdded';
					
	const _USEREMAILADDRESS = 'userEmailAddress';
					
	const _USERID = 'userID';
					
	const _USERNAME = 'userName';
					
	const _USEROWNER = 'userOwner';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastRoleField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWAPICALLS = 'allowAPICalls';
					
	const _ALLOWCONSOLEACCESS = 'allowConsoleAccess';
					
	const _CAPABILITIES = 'capabilities';
					
	const _COPYTONEWACCOUNTS = 'copyToNewAccounts';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _GRANTBYDEFAULT = 'grantByDefault';
					
	const _GRANTFUTURECAPABILITIES = 'grantFutureCapabilities';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHOWHOMETAB = 'showHomeTab';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastUserField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUTHENTICATIONMETHOD = 'authenticationMethod';
					
	const _DESCRIPTION = 'description';
					
	const _EMAILADDRESS = 'emailAddress';
					
	const _FAILEDSIGNINATTEMPTS = 'failedSignInAttempts';
					
	const _LASTACCOUNTID = 'lastAccountID';
					
	const _LASTFAILEDSIGNINATTEMPT = 'lastFailedSignInAttempt';
					
	const _LASTFAILEDSIGNINATTEMPTIPADDRESS = 'lastFailedSignInAttemptIPAddress';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _NAME = 'name';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PERMISSIONIDS = 'permissionIDs';
					
	const _POSSIBLEPASSWORDATTACKDETECTED = 'possiblePasswordAttackDetected';
					
	const _PREVENTPASSWORDATTACKS = 'preventPasswordAttacks';
					
	const _TIMEZONE = 'timeZone';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAccountField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACTIONLIMIT = 'actionLimit';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWCONSOLEACCESS = 'allowConsoleAccess';
					
	const _ALLOWOPENDELIVERY = 'allowOpenDelivery';
					
	const _ALLOWEDDELIVERYSITES = 'allowedDeliverySites';
					
	const _ALLOWEDUSERAGENTS = 'allowedUserAgents';
					
	const _AUTOMATICALLYCOLLECTPAYMENTSBYDEFAULT = 'automaticallyCollectPaymentsByDefault';
					
	const _AUTOMATICALLYDELETEEMPTYRELEASES = 'automaticallyDeleteEmptyReleases';
					
	const _AUTOMATICALLYDELETEEXPIREDCONTENT = 'automaticallyDeleteExpiredContent';
					
	const _AUTOMATICALLYGENERATETHUMBNAILS = 'automaticallyGenerateThumbnails';
					
	const _BANNERHTML = 'bannerHTML';
					
	const _CHILDACCOUNTIDS = 'childAccountIDs';
					
	const _CONTACTINFO = 'contactInfo';
					
	const _CONTENTPUBLISHINGISPUBLIC = 'contentPublishingIsPublic';
					
	const _CONTENTPUBLISHINGNETWORKS = 'contentPublishingNetworks';
					
	const _CONTENTPUBLISHINGPASSWORD = 'contentPublishingPassword';
					
	const _CONTENTPUBLISHINGURL = 'contentPublishingURL';
					
	const _CONTENTPUBLISHINGUSEMESSAGING = 'contentPublishingUseMessaging';
					
	const _CONTENTPUBLISHINGUSERNAME = 'contentPublishingUserName';
					
	const _DEFAULTAPPROVED = 'defaultApproved';
					
	const _DEFAULTCONTAINERPLAYLISTIDS = 'defaultContainerPlaylistIDs';
					
	const _DEFAULTCONTAINERPLAYLISTS = 'defaultContainerPlaylists';
					
	const _DEFAULTCONTENTAPPROVED = 'defaultContentApproved';
					
	const _DEFAULTCOPYRIGHT = 'defaultCopyright';
					
	const _DEFAULTFLVDOWNLOADSERVERID = 'defaultFLVDownloadServerID';
					
	const _DEFAULTFLVPUSHSERVERID = 'defaultFLVPushServerID';
					
	const _DEFAULTFLVSTORAGESERVERID = 'defaultFLVStorageServerID';
					
	const _DEFAULTFLVSTREAMINGSERVERID = 'defaultFLVStreamingServerID';
					
	const _DEFAULTINHERITEDSERVERIDS = 'defaultInheritedServerIDs';
					
	const _DEFAULTLANGUAGE = 'defaultLanguage';
					
	const _DEFAULTLICENSEIDS = 'defaultLicenseIDs';
					
	const _DEFAULTLICENSES = 'defaultLicenses';
					
	const _DEFAULTOTHERDOWNLOADSERVERID = 'defaultOtherDownloadServerID';
					
	const _DEFAULTOTHERPUSHSERVERID = 'defaultOtherPushServerID';
					
	const _DEFAULTOTHERSTORAGESERVERID = 'defaultOtherStorageServerID';
					
	const _DEFAULTOTHERSTREAMINGSERVERID = 'defaultOtherStreamingServerID';
					
	const _DEFAULTQTDOWNLOADSERVERID = 'defaultQTDownloadServerID';
					
	const _DEFAULTQTPUSHSERVERID = 'defaultQTPushServerID';
					
	const _DEFAULTQTSTORAGESERVERID = 'defaultQTStorageServerID';
					
	const _DEFAULTQTSTREAMINGSERVERID = 'defaultQTStreamingServerID';
					
	const _DEFAULTRATING = 'defaultRating';
					
	const _DEFAULTREALDOWNLOADSERVERID = 'defaultRealDownloadServerID';
					
	const _DEFAULTREALPUSHSERVERID = 'defaultRealPushServerID';
					
	const _DEFAULTREALSTORAGESERVERID = 'defaultRealStorageServerID';
					
	const _DEFAULTREALSTREAMINGSERVERID = 'defaultRealStreamingServerID';
					
	const _DEFAULTRESTRICTIONIDS = 'defaultRestrictionIDs';
					
	const _DEFAULTRESTRICTIONS = 'defaultRestrictions';
					
	const _DEFAULTTHUMBNAILSERVERID = 'defaultThumbnailServerID';
					
	const _DEFAULTTIMEZONE = 'defaultTimeZone';
					
	const _DEFAULTUSAGEPLANIDS = 'defaultUsagePlanIDs';
					
	const _DEFAULTUSAGEPLANS = 'defaultUsagePlans';
					
	const _DEFAULTWMDOWNLOADSERVERID = 'defaultWMDownloadServerID';
					
	const _DEFAULTWMPUSHSERVERID = 'defaultWMPushServerID';
					
	const _DEFAULTWMSTORAGESERVERID = 'defaultWMStorageServerID';
					
	const _DEFAULTWMSTREAMINGSERVERID = 'defaultWMStreamingServerID';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLEACCESSTORELEASEDMEDIAFILEURLS = 'disableAccessToReleasedMediaFileURLs';
					
	const _DISABLEDROPFOLDER = 'disableDropFolder';
					
	const _DISABLEMEDIAFILEENCODING = 'disableMediaFileEncoding';
					
	const _DISABLENEWACCOUNTS = 'disableNewAccounts';
					
	const _DISABLENEWDRMLICENSES = 'disableNewDRMLicenses';
					
	const _DISABLENEWLICENSES = 'disableNewLicenses';
					
	const _DISABLENEWSHARING = 'disableNewSharing';
					
	const _DISABLEPORTALS = 'disablePortals';
					
	const _DISABLESTANDALONETRACKING = 'disableStandAloneTracking';
					
	const _DISABLESTANDALONEUPLOADS = 'disableStandAloneUploads';
					
	const _DISABLESTOREFRONTS = 'disableStorefronts';
					
	const _DISABLETHUMBNAILGENERATION = 'disableThumbnailGeneration';
					
	const _DISABLED = 'disabled';
					
	const _DOMAIN = 'domain';
					
	const _DROPFOLDERFILEPATTERNS = 'dropFolderFilePatterns';
					
	const _ERRORMESSAGEBASEURL = 'errorMessageBaseURL';
					
	const _HASCHILDACCOUNTS = 'hasChildAccounts';
					
	const _HELPURL = 'helpURL';
					
	const _HOMETABHEADERHEIGHT = 'homeTabHeaderHeight';
					
	const _HOMETABURL = 'homeTabURL';
					
	const _INHERITEDSERVERIDS = 'inheritedServerIDs';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LIMITCONTENTBYENDUSERLOCATION = 'limitContentByEndUserLocation';
					
	const _LIMITTOACCOUNTSHARING = 'limitToAccountSharing';
					
	const _LIMITTOROLEIDS = 'limitToRoleIDs';
					
	const _LIMITTOROLES = 'limitToRoles';
					
	const _LOCKED = 'locked';
					
	const _LOGOURL = 'logoURL';
					
	const _MAINSITEURL = 'mainSiteURL';
					
	const _MAXIMUMAPIREQUESTSPERDAY = 'maximumAPIRequestsPerDay';
					
	const _MAXIMUMENCODINGPROFILETOTALBITRATE = 'maximumEncodingProfileTotalBitrate';
					
	const _MAXIMUMPAYMENTPERTRANSACTION = 'maximumPaymentPerTransaction';
					
	const _MAXIMUMRELEASEREQUESTSPERDAY = 'maximumReleaseRequestsPerDay';
					
	const _MAXIMUMUSAGEREPORTREQUESTSPERDAY = 'maximumUsageReportRequestsPerDay';
					
	const _METAFILEENCODING = 'metafileEncoding';
					
	const _NAME = 'name';
					
	const _NOTIFICATIONACTIONS = 'notificationActions';
					
	const _NOTIFICATIONISPUBLIC = 'notificationIsPublic';
					
	const _NOTIFICATIONITEMS = 'notificationItems';
					
	const _NOTIFICATIONNETWORKS = 'notificationNetworks';
					
	const _NOTIFICATIONPASSWORD = 'notificationPassword';
					
	const _NOTIFICATIONURL = 'notificationURL';
					
	const _NOTIFICATIONUSERNAME = 'notificationUserName';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PAYPAGEURL = 'payPageURL';
					
	const _PAYMENTFAILUREEMAILADDRESSES = 'paymentFailureEmailAddresses';
					
	const _PAYMENTGATEWAY = 'paymentGateway';
					
	const _PAYMENTGATEWAYACCOUNT = 'paymentGatewayAccount';
					
	const _PAYMENTGATEWAYPASSWORD = 'paymentGatewayPassword';
					
	const _PERMISSIONIDS = 'permissionIDs';
					
	const _PLAYERADMINSERVICEURL = 'playerAdminServiceURL';
					
	const _POSSIBLERATINGS = 'possibleRatings';
					
	const _RELEASEURL = 'releaseURL';
					
	const _STORAGEUSED = 'storageUsed';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _SUBDOMAIN = 'subdomain';
					
	const _TARGETCOUNTRIES = 'targetCountries';
					
	const _THUMBNAILADJUSTMENT = 'thumbnailAdjustment';
					
	const _THUMBNAILBACKGROUNDCOLOR = 'thumbnailBackgroundColor';
					
	const _THUMBNAILWIDTH = 'thumbnailWidth';
					
	const _TRACKBROWSERBYDEFAULT = 'trackBrowserByDefault';
					
	const _TRACKLOCATIONBYDEFAULT = 'trackLocationByDefault';
					
	const _TRANSCRIPTFOOTER = 'transcriptFooter';
					
	const _TRANSCRIPTHEADER = 'transcriptHeader';
					
	const _UPLOADQUOTA = 'uploadQuota';
					
	const _USEFLVSERVERSFORMPEG4 = 'useFLVServersForMPEG4';
					
	const _USEOWNERBANNERHTML = 'useOwnerBannerHTML';
					
	const _USEOWNERERRORMESSAGEBASEURL = 'useOwnerErrorMessageBaseURL';
					
	const _USEOWNERFLVDOWNLOADSERVER = 'useOwnerFLVDownloadServer';
					
	const _USEOWNERFLVPUSHSERVER = 'useOwnerFLVPushServer';
					
	const _USEOWNERFLVSTORAGESERVER = 'useOwnerFLVStorageServer';
					
	const _USEOWNERFLVSTREAMINGSERVER = 'useOwnerFLVStreamingServer';
					
	const _USEOWNERHOMETABHEADERHEIGHT = 'useOwnerHomeTabHeaderHeight';
					
	const _USEOWNERLOGOURL = 'useOwnerLogoURL';
					
	const _USEOWNERMAINSITEURL = 'useOwnerMainSiteURL';
					
	const _USEOWNEROTHERDOWNLOADSERVER = 'useOwnerOtherDownloadServer';
					
	const _USEOWNEROTHERPUSHSERVER = 'useOwnerOtherPushServer';
					
	const _USEOWNEROTHERSTORAGESERVER = 'useOwnerOtherStorageServer';
					
	const _USEOWNEROTHERSTREAMINGSERVER = 'useOwnerOtherStreamingServer';
					
	const _USEOWNERQTDOWNLOADSERVER = 'useOwnerQTDownloadServer';
					
	const _USEOWNERQTPUSHSERVER = 'useOwnerQTPushServer';
					
	const _USEOWNERQTSTORAGESERVER = 'useOwnerQTStorageServer';
					
	const _USEOWNERQTSTREAMINGSERVER = 'useOwnerQTStreamingServer';
					
	const _USEOWNERREALDOWNLOADSERVER = 'useOwnerRealDownloadServer';
					
	const _USEOWNERREALPUSHSERVER = 'useOwnerRealPushServer';
					
	const _USEOWNERREALSTORAGESERVER = 'useOwnerRealStorageServer';
					
	const _USEOWNERREALSTREAMINGSERVER = 'useOwnerRealStreamingServer';
					
	const _USEOWNERSTYLESHEETURL = 'useOwnerStylesheetURL';
					
	const _USEOWNERTHUMBNAILSERVER = 'useOwnerThumbnailServer';
					
	const _USEOWNERWMDOWNLOADSERVER = 'useOwnerWMDownloadServer';
					
	const _USEOWNERWMPUSHSERVER = 'useOwnerWMPushServer';
					
	const _USEOWNERWMSTORAGESERVER = 'useOwnerWMStorageServer';
					
	const _USEOWNERWMSTREAMINGSERVER = 'useOwnerWMStreamingServer';
					
	const _USEPAYMENTGATEWAYTESTMODE = 'usePaymentGatewayTestMode';
					
	const _VERSION = 'version';
					
	const _VISIBLETOACCOUNTIDS = 'visibleToAccountIDs';
					
	const _VISIBLETOACCOUNTS = 'visibleToAccounts';
					
	const _VISIBLETOALLACCOUNTS = 'visibleToAllAccounts';
					
	const _WMRMLICENSEACQUISITIONURL = 'wmrmLicenseAcquisitionURL';
					
	const _WMRMLICENSEKEYSEED = 'wmrmLicenseKeySeed';
					
	const _WMRMPRIVATEKEY = 'wmrmPrivateKey';
					
	const _WMRMPUBLICKEY = 'wmrmPublicKey';
					
	const _WMRMREVOCATIONPRIVATEKEY = 'wmrmRevocationPrivateKey';
					
	const _WMRMREVOCATIONPUBLICKEY = 'wmrmRevocationPublicKey';
					
	const _WRITEACTIONLIMIT = 'writeActionLimit';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCustomFieldField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWEDTEXTVALUES = 'allowedTextValues';
					
	const _AVAILABLEONSHAREDCONTENT = 'availableOnSharedContent';
					
	const _DEFAULTTEXTVALUE = 'defaultTextValue';
					
	const _DESCRIPTION = 'description';
					
	const _FIELDNAME = 'fieldName';
					
	const _FIELDTYPE = 'fieldType';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _INCLUDEINMETAFILES = 'includeInMetafiles';
					
	const _INCLUDEINRELEASES = 'includeInReleases';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LIMITTOAPIOBJECTS = 'limitToAPIObjects';
					
	const _LINESTODISPLAY = 'linesToDisplay';
					
	const _LOCKED = 'locked';
					
	const _NAMESPACE = 'namespace';
					
	const _NAMESPACEPREFIX = 'namespacePrefix';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHAREWITHACCOUNTIDS = 'shareWithAccountIDs';
					
	const _SHAREWITHACCOUNTS = 'shareWithAccounts';
					
	const _SHAREWITHALLACCOUNTS = 'shareWithAllAccounts';
					
	const _SHOWINMOREFIELDS = 'showInMoreFields';
					
	const _SUPPORTEDFORMATS = 'supportedFormats';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastLocationField extends SoapObject
{				
	const _ID = 'ID';
					
	const _URL = 'URL';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _DELIVERY = 'delivery';
					
	const _DESCRIPTION = 'description';
					
	const _HASSUBSTITUTIONURL = 'hasSubstitutionURL';
					
	const _INUSE = 'inUse';
					
	const _ISPUBLIC = 'isPublic';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MEDIAFILEIDS = 'mediaFileIDs';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PRIVATEKEY = 'privateKey';
					
	const _PROMPTSTODOWNLOAD = 'promptsToDownload';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREACTIVEFTP = 'requireActiveFTP';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STORAGENETWORKS = 'storageNetworks';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastServerField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AVAILABLEFORSTORAGE = 'availableForStorage';
					
	const _AVAILABLETOCHILDACCOUNTSBYDEFAULT = 'availableToChildAccountsByDefault';
					
	const _BACKUPSTREAMINGURL = 'backupStreamingURL';
					
	const _CUSTOM = 'custom';
					
	const _DELETEURL = 'deleteURL';
					
	const _DELIVERFROMSTORAGEFORHTTP = 'deliverFromStorageForHTTP';
					
	const _DELIVERSMETAFILES = 'deliversMetafiles';
					
	const _DELIVERY = 'delivery';
					
	const _DELIVERYPERCENTAGE = 'deliveryPercentage';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _DISPLAYTITLE = 'displayTitle';
					
	const _DOWNLOADURL = 'downloadURL';
					
	const _DROPFOLDERURLS = 'dropFolderURLs';
					
	const _ENABLEFILELISTURL = 'enableFileListURL';
					
	const _FILELISTOPTIONS = 'fileListOptions';
					
	const _FILELISTPASSWORD = 'fileListPassword';
					
	const _FILELISTURL = 'fileListURL';
					
	const _FILELISTUSERNAME = 'fileListUserName';
					
	const _FORMAT = 'format';
					
	const _GUID = 'guid';
					
	const _ICON = 'icon';
					
	const _INUSE = 'inUse';
					
	const _ISPUBLIC = 'isPublic';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MAXIMUMFOLDERCOUNT = 'maximumFolderCount';
					
	const _MEDIAFILEIDS = 'mediaFileIDs';
					
	const _OPTIMIZEFORMANYFILES = 'optimizeForManyFiles';
					
	const _ORGANIZEFILESBYOWNER = 'organizeFilesByOwner';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PID = 'pid';
					
	const _PRIVATEKEY = 'privateKey';
					
	const _PROMPTSTODOWNLOAD = 'promptsToDownload';
					
	const _PUBLISHINGPASSWORD = 'publishingPassword';
					
	const _PUBLISHINGURL = 'publishingURL';
					
	const _PUBLISHINGUSERNAME = 'publishingUserName';
					
	const _PULLURL = 'pullURL';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _RELEASEIDS = 'releaseIDs';
					
	const _REQUIREACTIVEFTP = 'requireActiveFTP';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STORAGENETWORKS = 'storageNetworks';
					
	const _STORAGEQUOTA = 'storageQuota';
					
	const _STORAGEURL = 'storageURL';
					
	const _STORAGEUSED = 'storageUsed';
					
	const _STREAMINGURL = 'streamingURL';
					
	const _SUPPORTSPUSH = 'supportsPush';
					
	const _TITLE = 'title';
					
	const _UPDATEFILELAYOUT = 'updateFileLayout';
					
	const _UPLOADBASEURLS = 'uploadBaseURLs';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastSystemTaskField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountId';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _DESCRIPTION = 'description';
					
	const _DESTINATION = 'destination';
					
	const _DESTINATIONLOCATION = 'destinationLocation';
					
	const _DIAGNOSTICS = 'diagnostics';
					
	const _FAILEDATTEMPTS = 'failedAttempts';
					
	const _ITEM = 'item';
					
	const _JOB = 'job';
					
	const _JOBID = 'jobID';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PERCENTCOMPLETE = 'percentComplete';
					
	const _REFRESH = 'refresh';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREDSERVICETOKEN = 'requiredServiceToken';
					
	const _SERVICETOKEN = 'serviceToken';
					
	const _SOURCE = 'source';
					
	const _SOURCELOCATION = 'sourceLocation';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TASKTYPE = 'taskType';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCustomCommandField extends SoapObject
{				
	const _ID = 'ID';
					
	const _URL = 'URL';
					
	const _URLPASSWORD = 'URLPassword';
					
	const _URLUSERNAME = 'URLUserName';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _CONFIRMATIONALERT = 'confirmationAlert';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _INDEX = 'index';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MAXIMUMITEMS = 'maximumItems';
					
	const _MAXIMUMITEMSALERT = 'maximumItemsAlert';
					
	const _MINIMUMITEMS = 'minimumItems';
					
	const _MINIMUMITEMSALERT = 'minimumItemsAlert';
					
	const _ONLYFOROWNEDITEMS = 'onlyForOwnedItems';
					
	const _ONLYFOROWNEDITEMSALERT = 'onlyForOwnedItemsAlert';
					
	const _OPENINNEWWINDOW = 'openInNewWindow';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REQUIREDCAPABILITYTYPES = 'requiredCapabilityTypes';
					
	const _SHOWASDIALOG = 'showAsDialog';
					
	const _SHOWSCROLLBARS = 'showScrollbars';
					
	const _SHOWTOREADONLYUSERS = 'showToReadOnlyUsers';
					
	const _SHOWTOSTANDARDUSERS = 'showToStandardUsers';
					
	const _TITLE = 'title';
					
	const _USESELECTION = 'useSelection';
					
	const _VERSION = 'version';
					
	const _VIEWS = 'views';
					
	const _WINDOWHEIGHT = 'windowHeight';
					
	const _WINDOWNAME = 'windowName';
					
	const _WINDOWWIDTH = 'windowWidth';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastDirectoryField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _GRANTACCESSIFUNAVAILABLE = 'grantAccessIfUnavailable';
					
	const _HOST = 'host';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PORT = 'port';
					
	const _PRIORITY = 'priority';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _SCOPE = 'scope';
					
	const _SEARCHPATTERN = 'searchPattern';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TITLE = 'title';
					
	const _USESSL = 'useSSL';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastJobField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUTOMATICALLYDELETE = 'automaticallyDelete';
					
	const _DESCRIPTION = 'description';
					
	const _HASFAILEDTASKS = 'hasFailedTasks';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PROCESSINORDER = 'processInOrder';
					
	const _READY = 'ready';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TASKSREMAINING = 'tasksRemaining';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastSystemStatusField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _BUILDDATE = 'buildDate';
					
	const _CURRENTDATE = 'currentDate';
					
	const _DESCRIPTION = 'description';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _QUEUEDCONNECTIONS = 'queuedConnections';
					
	const _ROOTACCOUNT = 'rootAccount';
					
	const _ROOTACCOUNTID = 'rootAccountID';
					
	const _SERVERADDRESS = 'serverAddress';
					
	const _SERVERNAME = 'serverName';
					
	const _SOFTWAREVERSION = 'softwareVersion';
					
	const _STARTDATE = 'startDate';
					
	const _UPTIME = 'upTime';
					
	const _UPTIMEWITHUNITS = 'upTimeWithUnits';
					
	const _USAGETRACKINGLOAD = 'usageTrackingLoad';
					
	const _VERSION = 'version';
					
	const _WEBXML = 'webXML';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastSystemRequestLogField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _CURRENTDATE = 'currentDate';
					
	const _DESCRIPTION = 'description';
					
	const _FAILEDAVERAGERESPONSETIME = 'failedAverageResponseTime';
					
	const _FAILEDAVERAGERESPONSETIMES = 'failedAverageResponseTimes';
					
	const _FAILEDREQUESTCOUNT = 'failedRequestCount';
					
	const _FAILEDREQUESTCOUNTS = 'failedRequestCounts';
					
	const _FAILEDREQUESTSPERHOUR = 'failedRequestsPerHour';
					
	const _FAILEDREQUESTSPERMINUTE = 'failedRequestsPerMinute';
					
	const _FAILEDREQUESTSPERSECOND = 'failedRequestsPerSecond';
					
	const _FAILURERATE = 'failureRate';
					
	const _FAILURERATES = 'failureRates';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REQUESTCOUNT = 'requestCount';
					
	const _REQUESTCOUNTS = 'requestCounts';
					
	const _REQUESTSPERHOUR = 'requestsPerHour';
					
	const _REQUESTSPERMINUTE = 'requestsPerMinute';
					
	const _REQUESTSPERSECOND = 'requestsPerSecond';
					
	const _SAMPLEENDDATE = 'sampleEndDate';
					
	const _SAMPLELENGTH = 'sampleLength';
					
	const _SAMPLESTARTDATE = 'sampleStartDate';
					
	const _SERVERADDRESS = 'serverAddress';
					
	const _SERVERNAME = 'serverName';
					
	const _SUCCESSFULAVERAGERESPONSETIME = 'successfulAverageResponseTime';
					
	const _SUCCESSFULAVERAGERESPONSETIMES = 'successfulAverageResponseTimes';
					
	const _SYSTEMREQUESTTYPE = 'systemRequestType';
					
	const _TOTALFAILEDREQUESTCOUNT = 'totalFailedRequestCount';
					
	const _TOTALREQUESTCOUNT = 'totalRequestCount';
					
	const _TOTALSUCCESSFULREQUESTCOUNT = 'totalSuccessfulRequestCount';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastPermissionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastPermissionField';
			case 'tieBreaker':
				return 'ComcastPermissionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastPermissionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastPermissionSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastRoleSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastRoleField';
			case 'tieBreaker':
				return 'ComcastRoleSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastRoleField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastRoleSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastUserSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastUserField';
			case 'tieBreaker':
				return 'ComcastUserSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastUserField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastUserSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastAccountSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastAccountField';
			case 'tieBreaker':
				return 'ComcastAccountSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastAccountField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastAccountSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastCustomFieldSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastCustomFieldField';
			case 'tieBreaker':
				return 'ComcastCustomFieldSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastCustomFieldField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastCustomFieldSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastLocationSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastLocationField';
			case 'tieBreaker':
				return 'ComcastLocationSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastLocationField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastLocationSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastServerSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastServerField';
			case 'tieBreaker':
				return 'ComcastServerSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastServerField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastServerSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastSystemTaskSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastSystemTaskField';
			case 'tieBreaker':
				return 'ComcastSystemTaskSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastSystemTaskField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastSystemTaskSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastCustomCommandSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastCustomCommandField';
			case 'tieBreaker':
				return 'ComcastCustomCommandSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastCustomCommandField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastCustomCommandSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastDirectorySort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastDirectoryField';
			case 'tieBreaker':
				return 'ComcastDirectorySort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastDirectoryField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastDirectorySort
	 **/
	public $tieBreaker;
				
}
	
class ComcastJobSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastJobField';
			case 'tieBreaker':
				return 'ComcastJobSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastJobField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastJobSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastCapabilityType extends SoapObject
{				
	const _FULLCONTROL = 'FullControl';
					
	const _VIEW = 'View';
					
	const _ADD = 'Add';
					
	const _EDIT = 'Edit';
					
	const _DELETE = 'Delete';
					
	const _APPROVE = 'Approve';
					
	const _VIEWSELF = 'ViewSelf';
					
	const _EDITSELF = 'EditSelf';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastFormat extends SoapObject
{				
	const _UNKNOWN = 'Unknown';
					
	const _ANY = 'Any';
					
	const _3GPP = '3GPP';
					
	const _3GPP2 = '3GPP2';
					
	const _AAC = 'AAC';
					
	const _ASX = 'ASX';
					
	const _AVI = 'AVI';
					
	const _BMP = 'BMP';
					
	const _CSS = 'CSS';
					
	const _DFXP = 'DFXP';
					
	const _DV = 'DV';
					
	const _EMF = 'EMF';
					
	const _EXE = 'EXE';
					
	const _EXCEL = 'Excel';
					
	const _F4M = 'F4M';
					
	const _FLV = 'FLV';
					
	const _FLX = 'FLX';
					
	const _FLASH = 'Flash';
					
	const _GIF = 'GIF';
					
	const _HTML = 'HTML';
					
	const _ISM = 'ISM';
					
	const _ICON = 'Icon';
					
	const _JPEG = 'JPEG';
					
	const _LXF = 'LXF';
					
	const _M3U = 'M3U';
					
	const _MP3 = 'MP3';
					
	const _MPEG = 'MPEG';
					
	const _MPEG4 = 'MPEG4';
					
	const _MSI = 'MSI';
					
	const _MXF = 'MXF';
					
	const _MOVE = 'Move';
					
	const _OGG = 'Ogg';
					
	const _PDF = 'PDF';
					
	const _PLS = 'PLS';
					
	const _PNG = 'PNG';
					
	const _PPT = 'PPT';
					
	const _QT = 'QT';
					
	const _RAM = 'RAM';
					
	const _REAL = 'Real';
					
	const _SAMI = 'SAMI';
					
	const _SCC = 'SCC';
					
	const _SMIL = 'SMIL';
					
	const _SRT = 'SRT';
					
	const _SCRIPT = 'Script';
					
	const _TIFF = 'TIFF';
					
	const _TEXT = 'Text';
					
	const _VAST = 'VAST';
					
	const _WAV = 'WAV';
					
	const _WM = 'WM';
					
	const _WEBM = 'WebM';
					
	const _WORD = 'Word';
					
	const _XML = 'XML';
					
	const _ZIP = 'Zip';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastDelivery extends SoapObject
{				
	const _ALL = 'All';
					
	const _DOWNLOADANDSTREAMING = 'DownloadAndStreaming';
					
	const _DOWNLOADANDPUSH = 'DownloadAndPush';
					
	const _STREAMINGANDPUSH = 'StreamingAndPush';
					
	const _DOWNLOAD = 'Download';
					
	const _STREAMING = 'Streaming';
					
	const _PUSH = 'Push';
					
	const _NONE = 'None';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastServerIcon extends SoapObject
{				
	const _ABACAST = 'Abacast';
					
	const _ACTIVATE = 'Activate';
					
	const _AKAMAI = 'Akamai';
					
	const _AMAZON_S3 = 'Amazon S3';
					
	const _ARCOSTREAM = 'ArcoStream';
					
	const _ASPERA = 'Aspera';
					
	const _BITTORRENT = 'BitTorrent';
					
	const _CDNETWORKS = 'CDNetworks';
					
	const _CISCO = 'Cisco';
					
	const _COMCAST = 'Comcast';
					
	const _CUSTOM = 'Custom';
					
	const _DIGITAL_FOUNTAIN = 'Digital Fountain';
					
	const _EDGECAST = 'EdgeCast';
					
	const _EXODUS = 'Exodus';
					
	const _EXTERNAL = 'External';
					
	const _FLASH_VIDEO = 'Flash Video';
					
	const _HIGHWINDS = 'Highwinds';
					
	const _LEVEL_3 = 'Level 3';
					
	const _LIMELIGHT_NETWORKS = 'Limelight Networks';
					
	const _MEDIA_ON_DEMAND = 'Media on Demand';
					
	const _MIRROR_IMAGE = 'Mirror Image';
					
	const _MOVE_NETWORKS = 'Move Networks';
					
	const _QUICKTIME = 'QuickTime';
					
	const _QWEST = 'Qwest';
					
	const _RBN = 'RBN';
					
	const _REALMEDIA = 'RealMedia';
					
	const _SAVVIS = 'Savvis';
					
	const _SPEEDERA = 'Speedera';
					
	const _THEPLATFORM = 'thePlatform';
					
	const _THUMBNAILS = 'Thumbnails';
					
	const _VELOCIX = 'Velocix';
					
	const _VERIVUE = 'Verivue';
					
	const _VITALSTREAM = 'VitalStream';
					
	const _WINDOWS_MEDIA = 'Windows Media';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastProtectionLevel extends SoapObject
{				
	const _DRM = 'DRM';
					
	const _LINK = 'Link';
					
	const _LINKANDDRM = 'LinkAndDRM';
					
	const _NONE = 'None';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastNotificationAction extends SoapObject
{				
	const _ADD = 'Add';
					
	const _MODIFY = 'Modify';
					
	const _DELETE = 'Delete';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastPaymentGateway extends SoapObject
{				
	const _CUSTOM = 'Custom';
					
	const _NONE = 'None';
					
	const _PAYPAL = 'PayPal';
					
	const _VERISIGNPAYFLOWPRO = 'VeriSignPayflowPro';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAdminView extends SoapObject
{				
	const _ACCOUNTS = 'Accounts';
					
	const _ASSET_TYPES = 'Asset Types';
					
	const _CATEGORIES = 'Categories';
					
	const _CHOICES = 'Choices';
					
	const _CUSTOM_COMMANDS = 'Custom Commands';
					
	const _CUSTOM_FIELDS = 'Custom Fields';
					
	const _DEFAULTS = 'Defaults';
					
	const _DIRECTORIES = 'Directories';
					
	const _ENCODING = 'Encoding';
					
	const _END_USER_PERMISSIONS = 'End-User Permissions';
					
	const _END_USERS = 'End-Users';
					
	const _FILES = 'Files';
					
	const _GENERAL_SETTINGS = 'General Settings';
					
	const _LICENSES = 'Licenses';
					
	const _MEDIA = 'Media';
					
	const _PAGES = 'Pages';
					
	const _PERMISSIONS = 'Permissions';
					
	const _PERSONAL_INFO = 'Personal Info';
					
	const _PLAYLISTS = 'Playlists';
					
	const _PORTALS = 'Portals';
					
	const _RELEASES = 'Releases';
					
	const _REQUEST_LOGS = 'Request Logs';
					
	const _RESTRICTIONS = 'Restrictions';
					
	const _ROLES = 'Roles';
					
	const _SERVERS = 'Servers';
					
	const _STOREFRONTS = 'Storefronts';
					
	const _SYSTEM_STATUS = 'System Status';
					
	const _SYSTEM_TASKS = 'System Tasks';
					
	const _TRANSACTIONS = 'Transactions';
					
	const _USAGE_PLANS = 'Usage Plans';
					
	const _USAGE_REPORTS = 'Usage Reports';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastDayOfWeek extends SoapObject
{				
	const _SUNDAY = 'Sunday';
					
	const _MONDAY = 'Monday';
					
	const _TUESDAY = 'Tuesday';
					
	const _WEDNESDAY = 'Wednesday';
					
	const _THURSDAY = 'Thursday';
					
	const _FRIDAY = 'Friday';
					
	const _SATURDAY = 'Saturday';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCreditCardType extends SoapObject
{				
	const _AMERICANEXPRESS = 'AmericanExpress';
					
	const _DISCOVER = 'Discover';
					
	const _JCB = 'JCB';
					
	const _MASTERCARD = 'MasterCard';
					
	const _NONE = 'None';
					
	const _OTHER = 'Other';
					
	const _VISA = 'Visa';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastContactInfo extends SoapObject
{				
	const _HIDDEN = 'Hidden';
					
	const _OPTIONAL = 'Optional';
					
	const _REQUIRED = 'Required';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStorefrontPageType extends SoapObject
{				
	const _CATEGORIES = 'Categories';
					
	const _CUSTOM = 'Custom';
					
	const _LICENSES = 'Licenses';
					
	const _MEDIA = 'Media';
					
	const _PLAYLISTS = 'Playlists';
					
	const _PORTAL = 'Portal';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastSystemRequestType extends SoapObject
{				
	const _API = 'API';
					
	const _DATASERVICE = 'DataService';
					
	const _DYNAMICCHOICE = 'DynamicChoice';
					
	const _FMSSERVICE = 'FMSService';
					
	const _GEOTARGETEDCHOICE = 'GeoTargetedChoice';
					
	const _LDAP = 'LDAP';
					
	const _LICENSESERVER = 'LicenseServer';
					
	const _METAFILEFETCH = 'MetafileFetch';
					
	const _NOTIFICATION = 'Notification';
					
	const _PORTALCONTENTLIST = 'PortalContentList';
					
	const _PORTALPLAYER = 'PortalPlayer';
					
	const _RELEASE = 'Release';
					
	const _RSS = 'RSS';
					
	const _USAGEREPORT = 'UsageReport';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastMedia extends ComcastContent
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfMediaField';
			case 'choiceIDs':
				return 'ComcastIDSet';
			case 'mediaFileIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfMediaField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $album;
				
	/**
	 * @var boolean
	 **/
	public $allowFastForwarding;
				
	/**
	 * @var boolean
	 **/
	public $canDelete;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $choiceIDs;
				
	/**
	 * @var string
	 **/
	public $genre;
				
	/**
	 * @var long
	 **/
	public $mediaFileCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var long
	 **/
	public $thumbnailMediaFileID;
				
}
	
class ComcastPossibleReleaseSettings extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPossibleReleaseSetting");	
	}
					
}
	
class ComcastContent extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'actualRetentionTimeUnits':
				return 'ComcastTimeUnits';
			case 'allLicenseIDs':
				return 'ComcastIDSet';
			case 'allLicenses':
				return 'ComcastArrayOfstring';
			case 'allRestrictionIDs':
				return 'ComcastIDSet';
			case 'allRestrictions':
				return 'ComcastArrayOfstring';
			case 'allUsagePlanIDs':
				return 'ComcastIDSet';
			case 'allUsagePlans':
				return 'ComcastArrayOfstring';
			case 'banner':
				return 'ComcastImageValue';
			case 'categories':
				return 'ComcastArrayOfstring';
			case 'categoryIDs':
				return 'ComcastIDSet';
			case 'containerPlaylistIDs':
				return 'ComcastIDSet';
			case 'containerPlaylists':
				return 'ComcastArrayOfstring';
			case 'contentType':
				return 'ComcastContentType';
			case 'formats':
				return 'ComcastArrayOfFormat';
			case 'language':
				return 'ComcastLanguage';
			case 'licenseIDs':
				return 'ComcastIDSet';
			case 'licenses':
				return 'ComcastArrayOfstring';
			case 'moreInfo':
				return 'ComcastHyperlinkValue';
			case 'possibleReleaseSettings':
				return 'ComcastPossibleReleaseSettings';
			case 'releaseIDs':
				return 'ComcastIDSet';
			case 'restrictionIDs':
				return 'ComcastIDSet';
			case 'restrictions':
				return 'ComcastArrayOfstring';
			case 'targetCountries':
				return 'ComcastArrayOfCountry';
			case 'targetRegions':
				return 'ComcastArrayOfstring';
			case 'usagePlanIDs':
				return 'ComcastIDSet';
			case 'usagePlans':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $PID;
				
	/**
	 * @var boolean
	 **/
	public $actualApproved;
				
	/**
	 * @var dateTime
	 **/
	public $actualAvailableDate;
				
	/**
	 * @var dateTime
	 **/
	public $actualExpirationDate;
				
	/**
	 * @var dateTime
	 **/
	public $actualRetentionDate;
				
	/**
	 * @var long
	 **/
	public $actualRetentionTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $actualRetentionTimeUnits;
				
	/**
	 * @var dateTime
	 **/
	public $actualUnapproveDate;
				
	/**
	 * @var dateTime
	 **/
	public $airdate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $allLicenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allLicenses;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $allRestrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allRestrictions;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $allUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allUsagePlans;
				
	/**
	 * @var boolean
	 **/
	public $applyInheritedRestrictions;
				
	/**
	 * @var boolean
	 **/
	public $approved;
				
	/**
	 * @var string
	 **/
	public $author;
				
	/**
	 * @var boolean
	 **/
	public $available;
				
	/**
	 * @var dateTime
	 **/
	public $availableDate;
				
	/**
	 * @var ComcastImageValue
	 **/
	public $banner;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $categories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $categoryIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $containerPlaylistIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $containerPlaylists;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var string
	 **/
	public $copyright;
				
	/**
	 * @var boolean
	 **/
	public $excludeTargetLocations;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var boolean
	 **/
	public $expired;
				
	/**
	 * @var string
	 **/
	public $externalID;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $formats;
				
	/**
	 * @var boolean
	 **/
	public $hasAvailableReleases;
				
	/**
	 * @var boolean
	 **/
	public $hasLicenses;
				
	/**
	 * @var boolean
	 **/
	public $hasRestrictions;
				
	/**
	 * @var boolean
	 **/
	public $hasTranscript;
				
	/**
	 * @var boolean
	 **/
	public $hasUsagePlans;
				
	/**
	 * @var long
	 **/
	public $highestBitrate;
				
	/**
	 * @var string
	 **/
	public $keywords;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $language;
				
	/**
	 * @var long
	 **/
	public $length;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $licenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $licenses;
				
	/**
	 * @var long
	 **/
	public $lowestBitrate;
				
	/**
	 * @var ComcastHyperlinkValue
	 **/
	public $moreInfo;
				
	/**
	 * @var ComcastPossibleReleaseSettings
	 **/
	public $possibleReleaseSettings;
				
	/**
	 * @var string
	 **/
	public $rating;
				
	/**
	 * @var long
	 **/
	public $releaseCount;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $restrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $restrictions;
				
	/**
	 * @var ComcastArrayOfCountry
	 **/
	public $targetCountries;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $targetRegions;
				
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
	public $transcript;
				
	/**
	 * @var string
	 **/
	public $transcriptURL;
				
	/**
	 * @var dateTime
	 **/
	public $unapproveDate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $usagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $usagePlans;
				
}
	
class ComcastPossibleReleaseSetting extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'contentType':
				return 'ComcastContentType';
			case 'delivery':
				return 'ComcastDelivery';
			case 'format':
				return 'ComcastFormat';
			case 'trueFormat':
				return 'ComcastFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var ComcastFormat
	 **/
	public $trueFormat;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var string
	 **/
	public $assetType;
				
	/**
	 * @var long
	 **/
	public $assetTypeID;
				
	/**
	 * @var long
	 **/
	public $encodingProfileID;
				
	/**
	 * @var string
	 **/
	public $encodingProfileTitle;
				
}
	
class ComcastMediaFileList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaFile");	
	}
					
}
	
class ComcastMediaFile extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfMediaFileField';
			case 'approved':
				return 'Comcastboolean';
			case 'assetTypeIDs':
				return 'ComcastIDSet';
			case 'assetTypes':
				return 'ComcastArrayOfstring';
			case 'contentType':
				return 'ComcastContentType';
			case 'expression':
				return 'ComcastExpression';
			case 'format':
				return 'ComcastFormat';
			case 'language':
				return 'ComcastLanguage';
			case 'mediaFileType':
				return 'ComcastMediaFileType';
			case 'storageServerIcon':
				return 'ComcastServerIcon';
			case 'trueFormat':
				return 'ComcastFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfMediaFileField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var dateTime
	 **/
	public $actualRetentionDate;
				
	/**
	 * @var boolean
	 **/
	public $allowRelease;
				
	/**
	 * @var Comcastboolean
	 **/
	public $approved;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $assetTypeIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $assetTypes;
				
	/**
	 * @var int
	 **/
	public $audioChannels;
				
	/**
	 * @var string
	 **/
	public $audioCodec;
				
	/**
	 * @var int
	 **/
	public $audioSampleRate;
				
	/**
	 * @var int
	 **/
	public $audioSampleSize;
				
	/**
	 * @var string
	 **/
	public $backupStreamingURL;
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var boolean
	 **/
	public $cacheNewFile;
				
	/**
	 * @var boolean
	 **/
	public $cached;
				
	/**
	 * @var boolean
	 **/
	public $canDelete;
				
	/**
	 * @var string
	 **/
	public $checksum;
				
	/**
	 * @var string
	 **/
	public $checksumAlgorithm;
				
	/**
	 * @var base64Binary
	 **/
	public $content;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var string
	 **/
	public $customFilePath;
				
	/**
	 * @var dateTime
	 **/
	public $deletedDate;
				
	/**
	 * @var string
	 **/
	public $drmKeyID;
				
	/**
	 * @var boolean
	 **/
	public $dynamic;
				
	/**
	 * @var boolean
	 **/
	public $encodeNew;
				
	/**
	 * @var long
	 **/
	public $encodingProfileID;
				
	/**
	 * @var string
	 **/
	public $encodingProfileTitle;
				
	/**
	 * @var ComcastExpression
	 **/
	public $expression;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var float
	 **/
	public $frameRate;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var int
	 **/
	public $height;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var boolean
	 **/
	public $isDefault;
				
	/**
	 * @var boolean
	 **/
	public $isThumbnail;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $language;
				
	/**
	 * @var dateTime
	 **/
	public $lastCached;
				
	/**
	 * @var long
	 **/
	public $length;
				
	/**
	 * @var long
	 **/
	public $locationID;
				
	/**
	 * @var ComcastMediaFileType
	 **/
	public $mediaFileType;
				
	/**
	 * @var long
	 **/
	public $mediaID;
				
	/**
	 * @var string
	 **/
	public $originalLocation;
				
	/**
	 * @var string
	 **/
	public $parentDRMKeyID;
				
	/**
	 * @var boolean
	 **/
	public $protectedWithDRM;
				
	/**
	 * @var string
	 **/
	public $protectionScheme;
				
	/**
	 * @var string
	 **/
	public $requiredFileName;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var long
	 **/
	public $sourceMediaFileID;
				
	/**
	 * @var long
	 **/
	public $sourceTime;
				
	/**
	 * @var string
	 **/
	public $storage;
				
	/**
	 * @var long
	 **/
	public $storageServerID;
				
	/**
	 * @var ComcastServerIcon
	 **/
	public $storageServerIcon;
				
	/**
	 * @var string
	 **/
	public $storedFileName;
				
	/**
	 * @var string
	 **/
	public $storedFilePath;
				
	/**
	 * @var string
	 **/
	public $streamingURL;
				
	/**
	 * @var long
	 **/
	public $systemTaskID;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var ComcastFormat
	 **/
	public $trueFormat;
				
	/**
	 * @var boolean
	 **/
	public $undelete;
				
	/**
	 * @var boolean
	 **/
	public $usedAsMediaThumbnail;
				
	/**
	 * @var boolean
	 **/
	public $verify;
				
	/**
	 * @var string
	 **/
	public $videoCodec;
				
	/**
	 * @var int
	 **/
	public $width;
				
}
	
class ComcastAddContentOptions extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'encodingProfileTitles':
				return 'ComcastArrayOfstring';
			case 'createReleases':
				return 'ComcastDelivery';
			case 'releaseDefaults':
				return 'ComcastRelease';
			case 'releaseServerIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var boolean
	 **/
	public $generateThumbnail;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $encodingProfileTitles;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $createReleases;
				
	/**
	 * @var string
	 **/
	public $releaseOutletAccount;
				
	/**
	 * @var ComcastRelease
	 **/
	public $releaseDefaults;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseServerIDs;
				
	/**
	 * @var boolean
	 **/
	public $publish;
				
	/**
	 * @var boolean
	 **/
	public $deleteSource;
				
}
	
class ComcastRelease extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfReleaseField';
			case 'actualLimitToEndUserNames':
				return 'ComcastArrayOfstring';
			case 'actualLimitToExternalGroups':
				return 'ComcastArrayOfstring';
			case 'appliedContainerPlaylistIDs':
				return 'ComcastIDSet';
			case 'appliedContainerPlaylists':
				return 'ComcastArrayOfstring';
			case 'appliedLicenseIDs':
				return 'ComcastIDSet';
			case 'appliedLicenses':
				return 'ComcastArrayOfstring';
			case 'appliedRestrictionIDs':
				return 'ComcastIDSet';
			case 'appliedRestrictions':
				return 'ComcastArrayOfstring';
			case 'backupMediaFileURLs':
				return 'ComcastArrayOfstring';
			case 'checksums':
				return 'ComcastArrayOfstring';
			case 'containerPlaylistIDs':
				return 'ComcastIDSet';
			case 'containerPlaylists':
				return 'ComcastArrayOfstring';
			case 'contentAllUsagePlanIDs':
				return 'ComcastIDSet';
			case 'contentAllUsagePlans':
				return 'ComcastArrayOfstring';
			case 'contentBanner':
				return 'ComcastImageValue';
			case 'contentCategories':
				return 'ComcastArrayOfstring';
			case 'contentCategoryIDs':
				return 'ComcastIDSet';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'contentContentType':
				return 'ComcastContentType';
			case 'contentFormats':
				return 'ComcastArrayOfFormat';
			case 'contentLanguage':
				return 'ComcastLanguage';
			case 'contentMoreInfo':
				return 'ComcastHyperlinkValue';
			case 'contentPossibleReleaseSettings':
				return 'ComcastPossibleReleaseSettings';
			case 'contentStatus':
				return 'ComcastStatus';
			case 'contentStatusDetail':
				return 'ComcastStatusDetail';
			case 'contentTargetCountries':
				return 'ComcastArrayOfCountry';
			case 'contentTargetRegions':
				return 'ComcastArrayOfstring';
			case 'contentType':
				return 'ComcastContentType';
			case 'delivery':
				return 'ComcastDelivery';
			case 'format':
				return 'ComcastFormat';
			case 'licenseIDs':
				return 'ComcastIDSet';
			case 'licenses':
				return 'ComcastArrayOfstring';
			case 'mediaFileURLs':
				return 'ComcastArrayOfstring';
			case 'networkServerIcon':
				return 'ComcastServerIcon';
			case 'protectionLevel':
				return 'ComcastProtectionLevel';
			case 'settings':
				return 'ComcastPossibleReleaseSetting';
			case 'systemTaskIDs':
				return 'ComcastIDSet';
			case 'trueFormat':
				return 'ComcastFormat';
			case 'contentCustomData':
				return 'ComcastCustomData';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfReleaseField
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
	 * @var boolean
	 **/
	public $actualApproved;
				
	/**
	 * @var dateTime
	 **/
	public $actualAvailableDate;
				
	/**
	 * @var dateTime
	 **/
	public $actualExpirationDate;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $actualLimitToEndUserNames;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $actualLimitToExternalGroups;
				
	/**
	 * @var dateTime
	 **/
	public $actualUnapproveDate;
				
	/**
	 * @var string
	 **/
	public $adPolicyId;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $appliedContainerPlaylistIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $appliedContainerPlaylists;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $appliedLicenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $appliedLicenses;
				
	/**
	 * @var string
	 **/
	public $appliedParentLicense;
				
	/**
	 * @var long
	 **/
	public $appliedParentLicenseID;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $appliedRestrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $appliedRestrictions;
				
	/**
	 * @var boolean
	 **/
	public $applyInheritedContainerPlaylists;
				
	/**
	 * @var boolean
	 **/
	public $applyInheritedLicenses;
				
	/**
	 * @var boolean
	 **/
	public $approved;
				
	/**
	 * @var string
	 **/
	public $assetType;
				
	/**
	 * @var long
	 **/
	public $assetTypeID;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $backupMediaFileURLs;
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $checksums;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $containerPlaylistIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $containerPlaylists;
				
	/**
	 * @var dateTime
	 **/
	public $contentAdded;
				
	/**
	 * @var dateTime
	 **/
	public $contentAirdate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $contentAllUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentAllUsagePlans;
				
	/**
	 * @var boolean
	 **/
	public $contentApproved;
				
	/**
	 * @var string
	 **/
	public $contentAuthor;
				
	/**
	 * @var ComcastImageValue
	 **/
	public $contentBanner;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentCategories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $contentCategoryIDs;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentContentType;
				
	/**
	 * @var string
	 **/
	public $contentCopyright;
				
	/**
	 * @var string
	 **/
	public $contentDescription;
				
	/**
	 * @var boolean
	 **/
	public $contentExcludeTargetLocations;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $contentFormats;
				
	/**
	 * @var boolean
	 **/
	public $contentHasLicenses;
				
	/**
	 * @var boolean
	 **/
	public $contentHasRestrictions;
				
	/**
	 * @var boolean
	 **/
	public $contentHasTranscript;
				
	/**
	 * @var boolean
	 **/
	public $contentHasUsagePlans;
				
	/**
	 * @var long
	 **/
	public $contentHighestBitrate;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var string
	 **/
	public $contentKeywords;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $contentLanguage;
				
	/**
	 * @var dateTime
	 **/
	public $contentLastModified;
				
	/**
	 * @var long
	 **/
	public $contentLength;
				
	/**
	 * @var long
	 **/
	public $contentLowestBitrate;
				
	/**
	 * @var ComcastHyperlinkValue
	 **/
	public $contentMoreInfo;
				
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
	public $contentPID;
				
	/**
	 * @var ComcastPossibleReleaseSettings
	 **/
	public $contentPossibleReleaseSettings;
				
	/**
	 * @var string
	 **/
	public $contentRating;
				
	/**
	 * @var ComcastStatus
	 **/
	public $contentStatus;
				
	/**
	 * @var ComcastStatusDetail
	 **/
	public $contentStatusDetail;
				
	/**
	 * @var ComcastArrayOfCountry
	 **/
	public $contentTargetCountries;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentTargetRegions;
				
	/**
	 * @var string
	 **/
	public $contentThumbnailURL;
				
	/**
	 * @var string
	 **/
	public $contentTitle;
				
	/**
	 * @var string
	 **/
	public $contentTranscript;
				
	/**
	 * @var string
	 **/
	public $contentTranscriptURL;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var string
	 **/
	public $customMediaFilePath;
				
	/**
	 * @var string
	 **/
	public $delayedPID;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var string
	 **/
	public $drmKeyID;
				
	/**
	 * @var long
	 **/
	public $encodingProfileID;
				
	/**
	 * @var string
	 **/
	public $encodingProfileTitle;
				
	/**
	 * @var string
	 **/
	public $externalID;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var boolean
	 **/
	public $hasLicenses;
				
	/**
	 * @var int
	 **/
	public $height;
				
	/**
	 * @var string
	 **/
	public $licenseAcquisitionURL;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $licenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $licenses;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $mediaFileURLs;
				
	/**
	 * @var float
	 **/
	public $minimumDRMSDKVersion;
				
	/**
	 * @var string
	 **/
	public $network;
				
	/**
	 * @var long
	 **/
	public $networkServerID;
				
	/**
	 * @var ComcastServerIcon
	 **/
	public $networkServerIcon;
				
	/**
	 * @var string
	 **/
	public $parameters;
				
	/**
	 * @var ComcastProtectionLevel
	 **/
	public $protectionLevel;
				
	/**
	 * @var boolean
	 **/
	public $refresh;
				
	/**
	 * @var boolean
	 **/
	public $requireIndividualization;
				
	/**
	 * @var string
	 **/
	public $requiredMediaFileName;
				
	/**
	 * @var string
	 **/
	public $restrictionId;
				
	/**
	 * @var ComcastPossibleReleaseSetting
	 **/
	public $settings;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var boolean
	 **/
	public $supportedByFlashPlayer;
				
	/**
	 * @var boolean
	 **/
	public $supportedByQuickTimePlayer;
				
	/**
	 * @var boolean
	 **/
	public $supportedByRealMediaPlayer;
				
	/**
	 * @var boolean
	 **/
	public $supportedByWindowsMediaPlayer;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $systemTaskIDs;
				
	/**
	 * @var ComcastFormat
	 **/
	public $trueFormat;
				
	/**
	 * @var boolean
	 **/
	public $unapprovePreviousReleaseOnPIDChange;
				
	/**
	 * @var int
	 **/
	public $width;
				
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
	 * @var ComcastCustomData
	 **/
	public $contentCustomData;
				
}
	
class ComcastChoiceList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastChoice");	
	}
					
}
	
class ComcastChoice extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfChoiceField';
			case 'categories':
				return 'ComcastArrayOfstring';
			case 'categoryIDs':
				return 'ComcastIDSet';
			case 'contentAllUsagePlanIDs':
				return 'ComcastIDSet';
			case 'contentAllUsagePlans':
				return 'ComcastArrayOfstring';
			case 'contentBanner':
				return 'ComcastImageValue';
			case 'contentCategories':
				return 'ComcastArrayOfstring';
			case 'contentCategoryIDs':
				return 'ComcastIDSet';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'contentContentType':
				return 'ComcastContentType';
			case 'contentFormats':
				return 'ComcastArrayOfFormat';
			case 'contentLanguage':
				return 'ComcastLanguage';
			case 'contentMoreInfo':
				return 'ComcastHyperlinkValue';
			case 'contentPossibleReleaseSettings':
				return 'ComcastPossibleReleaseSettings';
			case 'contentStatus':
				return 'ComcastStatus';
			case 'contentStatusDetail':
				return 'ComcastStatusDetail';
			case 'contentTargetCountries':
				return 'ComcastArrayOfCountry';
			case 'contentTargetRegions':
				return 'ComcastArrayOfstring';
			case 'contentType':
				return 'ComcastContentType';
			case 'contentCustomData':
				return 'ComcastCustomData';
			case 'choiceType':
				return 'ComcastChoiceType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfChoiceField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $allowGlobalContent;
				
	/**
	 * @var boolean
	 **/
	public $allowHigherBitrates;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $categories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $categoryIDs;
				
	/**
	 * @var int
	 **/
	public $choiceLimit;
				
	/**
	 * @var dateTime
	 **/
	public $contentAdded;
				
	/**
	 * @var dateTime
	 **/
	public $contentAirdate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $contentAllUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentAllUsagePlans;
				
	/**
	 * @var boolean
	 **/
	public $contentApproved;
				
	/**
	 * @var string
	 **/
	public $contentAuthor;
				
	/**
	 * @var ComcastImageValue
	 **/
	public $contentBanner;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentCategories;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $contentCategoryIDs;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentContentType;
				
	/**
	 * @var string
	 **/
	public $contentCopyright;
				
	/**
	 * @var string
	 **/
	public $contentDescription;
				
	/**
	 * @var boolean
	 **/
	public $contentExcludeTargetLocations;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $contentFormats;
				
	/**
	 * @var boolean
	 **/
	public $contentHasLicenses;
				
	/**
	 * @var boolean
	 **/
	public $contentHasRestrictions;
				
	/**
	 * @var boolean
	 **/
	public $contentHasTranscript;
				
	/**
	 * @var boolean
	 **/
	public $contentHasUsagePlans;
				
	/**
	 * @var long
	 **/
	public $contentHighestBitrate;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var string
	 **/
	public $contentKeywords;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $contentLanguage;
				
	/**
	 * @var dateTime
	 **/
	public $contentLastModified;
				
	/**
	 * @var long
	 **/
	public $contentLength;
				
	/**
	 * @var long
	 **/
	public $contentLowestBitrate;
				
	/**
	 * @var ComcastHyperlinkValue
	 **/
	public $contentMoreInfo;
				
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
	public $contentPID;
				
	/**
	 * @var ComcastPossibleReleaseSettings
	 **/
	public $contentPossibleReleaseSettings;
				
	/**
	 * @var string
	 **/
	public $contentRating;
				
	/**
	 * @var ComcastStatus
	 **/
	public $contentStatus;
				
	/**
	 * @var ComcastStatusDetail
	 **/
	public $contentStatusDetail;
				
	/**
	 * @var ComcastArrayOfCountry
	 **/
	public $contentTargetCountries;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentTargetRegions;
				
	/**
	 * @var string
	 **/
	public $contentThumbnailURL;
				
	/**
	 * @var string
	 **/
	public $contentTitle;
				
	/**
	 * @var string
	 **/
	public $contentTranscript;
				
	/**
	 * @var string
	 **/
	public $contentTranscriptURL;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var int
	 **/
	public $index;
				
	/**
	 * @var boolean
	 **/
	public $matchTargetLocation;
				
	/**
	 * @var float
	 **/
	public $playPercentage;
				
	/**
	 * @var long
	 **/
	public $playlistID;
				
	/**
	 * @var boolean
	 **/
	public $skipNextChoiceIfMatch;
				
	/**
	 * @var string
	 **/
	public $tags;
				
	/**
	 * @var ComcastCustomData
	 **/
	public $contentCustomData;
				
	/**
	 * @var ComcastChoiceType
	 **/
	public $choiceType;
				
}
	
class ComcastRestrictionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRestriction");	
	}
					
}
	
class ComcastRestriction extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRestrictionField';
			case 'availableTimeUnits':
				return 'ComcastTimeUnits';
			case 'delivery':
				return 'ComcastDelivery';
			case 'expirationTimeUnits':
				return 'ComcastTimeUnits';
			case 'limitToEndUserNames':
				return 'ComcastArrayOfstring';
			case 'limitToExternalGroups':
				return 'ComcastArrayOfstring';
			case 'retentionTimeUnits':
				return 'ComcastTimeUnits';
			case 'unapproveTimeUnits':
				return 'ComcastTimeUnits';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRestrictionField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $availableDate;
				
	/**
	 * @var long
	 **/
	public $availableTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $availableTimeUnits;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var long
	 **/
	public $expirationTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $expirationTimeUnits;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToEndUserNames;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToExternalGroups;
				
	/**
	 * @var boolean
	 **/
	public $requireDRM;
				
	/**
	 * @var dateTime
	 **/
	public $retentionDate;
				
	/**
	 * @var long
	 **/
	public $retentionTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $retentionTimeUnits;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var dateTime
	 **/
	public $unapproveDate;
				
	/**
	 * @var long
	 **/
	public $unapproveTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $unapproveTimeUnits;
				
	/**
	 * @var boolean
	 **/
	public $useAirdate;
				
}
	
class ComcastCategoryList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCategory");	
	}
					
}
	
class ComcastCategory extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCategoryField';
			case 'defaultLicenseIDs':
				return 'ComcastIDSet';
			case 'defaultLicenses':
				return 'ComcastArrayOfstring';
			case 'defaultRestrictionIDs':
				return 'ComcastIDSet';
			case 'defaultRestrictions':
				return 'ComcastArrayOfstring';
			case 'defaultUsagePlanIDs':
				return 'ComcastIDSet';
			case 'defaultUsagePlans':
				return 'ComcastArrayOfstring';
			case 'depth':
				return 'Comcastint';
			case 'index':
				return 'Comcastint';
			case 'mediaIDs':
				return 'ComcastIDSet';
			case 'playlistIDs':
				return 'ComcastIDSet';
			case 'treeOrder':
				return 'Comcastint';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCategoryField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultLicenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultLicenses;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultRestrictionIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultRestrictions;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $defaultUsagePlanIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $defaultUsagePlans;
				
	/**
	 * @var Comcastint
	 **/
	public $depth;
				
	/**
	 * @var string
	 **/
	public $fullTitle;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var boolean
	 **/
	public $hasChildren;
				
	/**
	 * @var Comcastint
	 **/
	public $index;
				
	/**
	 * @var string
	 **/
	public $label;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaIDs;
				
	/**
	 * @var string
	 **/
	public $parentCategory;
				
	/**
	 * @var long
	 **/
	public $parentCategoryId;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $playlistIDs;
				
	/**
	 * @var string
	 **/
	public $scheme;
				
	/**
	 * @var boolean
	 **/
	public $showInPicker;
				
	/**
	 * @var boolean
	 **/
	public $showInPortals;
				
	/**
	 * @var string
	 **/
	public $sortByPlaylist;
				
	/**
	 * @var long
	 **/
	public $sortByPlaylistID;
				
	/**
	 * @var string
	 **/
	public $thumbnailURL;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var Comcastint
	 **/
	public $treeOrder;
				
}
	
class ComcastMediaList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMedia");	
	}
					
}
	
class ComcastPlaylistList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPlaylist");	
	}
					
}
	
class ComcastPlaylist extends ComcastContent
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPlaylistField';
			case 'choiceIDs':
				return 'ComcastIDList';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPlaylistField
	 **/
	public $template;
				
	/**
	 * @var long
	 **/
	public $choiceCount;
				
	/**
	 * @var ComcastIDList
	 **/
	public $choiceIDs;
				
	/**
	 * @var boolean
	 **/
	public $shufflePlay;
				
}
	
class ComcastReleaseList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRelease");	
	}
					
}
	
class ComcastRequestList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRequest");	
	}
					
}
	
class ComcastRequest extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRequestField';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'contentType':
				return 'ComcastContentType';
			case 'country':
				return 'ComcastCountry';
			case 'delivery':
				return 'ComcastDelivery';
			case 'format':
				return 'ComcastFormat';
			case 'language':
				return 'ComcastLanguage';
			case 'requestDayOfWeek':
				return 'ComcastDayOfWeek';
			case 'requestMonthOnly':
				return 'Comcastint';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRequestField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $affiliate;
				
	/**
	 * @var string
	 **/
	public $assetType;
				
	/**
	 * @var string
	 **/
	public $author;
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var long
	 **/
	public $bitrateInKbps;
				
	/**
	 * @var string
	 **/
	public $browser;
				
	/**
	 * @var float
	 **/
	public $buffering;
				
	/**
	 * @var string
	 **/
	public $categories;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var long
	 **/
	public $contentIDForGroup;
				
	/**
	 * @var string
	 **/
	public $contentOwner;
				
	/**
	 * @var long
	 **/
	public $contentOwnerAccountID;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastCountry
	 **/
	public $country;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var string
	 **/
	public $encodingProfile;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var string
	 **/
	public $inPlaylist;
				
	/**
	 * @var long
	 **/
	public $inPlaylistID;
				
	/**
	 * @var long
	 **/
	public $inPlaylistIDForGroup;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $language;
				
	/**
	 * @var long
	 **/
	public $length;
				
	/**
	 * @var long
	 **/
	public $lengthPlayed;
				
	/**
	 * @var long
	 **/
	public $loadTime;
				
	/**
	 * @var string
	 **/
	public $network;
				
	/**
	 * @var long
	 **/
	public $networkServerID;
				
	/**
	 * @var string
	 **/
	public $operatingSystem;
				
	/**
	 * @var string
	 **/
	public $outlet;
				
	/**
	 * @var long
	 **/
	public $outletAccountID;
				
	/**
	 * @var float
	 **/
	public $played;
				
	/**
	 * @var string
	 **/
	public $player;
				
	/**
	 * @var string
	 **/
	public $portal;
				
	/**
	 * @var float
	 **/
	public $quality;
				
	/**
	 * @var string
	 **/
	public $rating;
				
	/**
	 * @var string
	 **/
	public $region;
				
	/**
	 * @var long
	 **/
	public $requestCount;
				
	/**
	 * @var dateTime
	 **/
	public $requestDate;
				
	/**
	 * @var dateTime
	 **/
	public $requestDateOnly;
				
	/**
	 * @var ComcastDayOfWeek
	 **/
	public $requestDayOfWeek;
				
	/**
	 * @var float
	 **/
	public $requestHour;
				
	/**
	 * @var dateTime
	 **/
	public $requestMonth;
				
	/**
	 * @var Comcastint
	 **/
	public $requestMonthOnly;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var long
	 **/
	public $trackingCount;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}
	
class ComcastEncodingProfileList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfile");	
	}
					
}
	
class ComcastEncodingProfile extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfEncodingProfileField';
			case 'audioBitrateMode':
				return 'ComcastBitrateMode';
			case 'contentType':
				return 'ComcastContentType';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
			case 'format':
				return 'ComcastFormat';
			case 'hinting':
				return 'ComcastHinting';
			case 'videoBitrateMode':
				return 'ComcastBitrateMode';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfEncodingProfileField
	 **/
	public $template;
				
	/**
	 * @var long
	 **/
	public $audioBitrate;
				
	/**
	 * @var ComcastBitrateMode
	 **/
	public $audioBitrateMode;
				
	/**
	 * @var int
	 **/
	public $audioBitsPerSample;
				
	/**
	 * @var int
	 **/
	public $audioChannels;
				
	/**
	 * @var long
	 **/
	public $audioCodecID;
				
	/**
	 * @var string
	 **/
	public $audioCodecTitle;
				
	/**
	 * @var long
	 **/
	public $audioSampleRate;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var boolean
	 **/
	public $correctForRepeatedFrames;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var string
	 **/
	public $externalEncodingProfileID;
				
	/**
	 * @var string
	 **/
	public $fileExtension;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var ComcastHinting
	 **/
	public $hinting;
				
	/**
	 * @var long
	 **/
	public $imageHeight;
				
	/**
	 * @var float
	 **/
	public $imageQuality;
				
	/**
	 * @var long
	 **/
	public $imageWidth;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var long
	 **/
	public $maximumAudioBitrate;
				
	/**
	 * @var long
	 **/
	public $maximumAudioBuffering;
				
	/**
	 * @var int
	 **/
	public $maximumPacketDuration;
				
	/**
	 * @var int
	 **/
	public $maximumPacketSize;
				
	/**
	 * @var long
	 **/
	public $maximumVideoBitrate;
				
	/**
	 * @var long
	 **/
	public $maximumVideoBuffering;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForEncodingSpeed;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForPortableDevices;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var long
	 **/
	public $totalBitrate;
				
	/**
	 * @var long
	 **/
	public $videoBitrate;
				
	/**
	 * @var ComcastBitrateMode
	 **/
	public $videoBitrateMode;
				
	/**
	 * @var long
	 **/
	public $videoCodecID;
				
	/**
	 * @var string
	 **/
	public $videoCodecTitle;
				
	/**
	 * @var float
	 **/
	public $videoFrameRate;
				
	/**
	 * @var long
	 **/
	public $videoKeyFrameInterval;
				
}
	
class ComcastAssetTypeList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAssetType");	
	}
					
}
	
class ComcastAssetType extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfAssetTypeField';
			case 'shareWithAccountIDs':
				return 'ComcastIDSet';
			case 'shareWithAccounts':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfAssetTypeField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $applyByDefault;
				
	/**
	 * @var boolean
	 **/
	public $applyToThumbnailsByDefault;
				
	/**
	 * @var string
	 **/
	public $guid;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $shareWithAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $shareWithAccounts;
				
	/**
	 * @var boolean
	 **/
	public $shareWithAllAccounts;
				
	/**
	 * @var string
	 **/
	public $title;
				
}
	
class ComcastCodec extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'contentType':
				return 'ComcastContentType';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
			case 'bitrateModes':
				return 'ComcastArrayOfBitrateMode';
			case 'possibleTargetForats':
				return 'ComcastArrayOfFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var long
	 **/
	public $id;
				
	/**
	 * @var ComcastArrayOfBitrateMode
	 **/
	public $bitrateModes;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $possibleTargetForats;
				
}
	
class ComcastAddContentResults extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'mediaFileIDs':
				return 'ComcastIDList';
			case 'releaseIDs':
				return 'ComcastIDList';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var long
	 **/
	public $mediaID;
				
	/**
	 * @var ComcastIDList
	 **/
	public $mediaFileIDs;
				
	/**
	 * @var ComcastIDList
	 **/
	public $releaseIDs;
				
}
	
class ComcastCodecs extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCodec");	
	}
					
}
	
class ComcastPossibleAudioEncodings extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPossibleAudioEncoding");	
	}
					
}
	
class ComcastPossibleAudioEncoding extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'bitrateMode':
				return 'ComcastBitrateMode';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var ComcastBitrateMode
	 **/
	public $bitrateMode;
				
	/**
	 * @var int
	 **/
	public $bitsPerSample;
				
	/**
	 * @var int
	 **/
	public $channels;
				
	/**
	 * @var long
	 **/
	public $codecID;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var long
	 **/
	public $sampleRate;
				
}
	
class ComcastArrayOfRestrictionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRestrictionField");	
	}
					
}
	
class ComcastArrayOfCategoryField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCategoryField");	
	}
					
}
	
class ComcastArrayOfChoiceField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastChoiceField");	
	}
					
}
	
class ComcastArrayOfMediaField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaField");	
	}
					
}
	
class ComcastArrayOfMediaFileField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaFileField");	
	}
					
}
	
class ComcastArrayOfPlaylistField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPlaylistField");	
	}
					
}
	
class ComcastArrayOfReleaseField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastReleaseField");	
	}
					
}
	
class ComcastArrayOfRequestField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRequestField");	
	}
					
}
	
class ComcastArrayOfEncodingProfileField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEncodingProfileField");	
	}
					
}
	
class ComcastArrayOfAssetTypeField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAssetTypeField");	
	}
					
}
	
class ComcastArrayOfBitrateMode extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastBitrateMode");	
	}
					
}
	
class ComcastContentType extends SoapObject
{				
	const _ANIMATION = 'Animation';
					
	const _AUDIO = 'Audio';
					
	const _DOCUMENT = 'Document';
					
	const _EXECUTABLE = 'Executable';
					
	const _IMAGE = 'Image';
					
	const _MIXED = 'Mixed';
					
	const _UNKNOWN = 'Unknown';
					
	const _VIDEO = 'Video';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastExpression extends SoapObject
{				
	const _FULL = 'Full';
					
	const _NONSTOP = 'NonStop';
					
	const _SAMPLE = 'Sample';
					
	const _UNKNOWN = 'Unknown';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastMediaFileType extends SoapObject
{				
	const _EXTERNAL = 'External';
					
	const _INTERNAL = 'Internal';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastContentClass extends SoapObject
{				
	const _MEDIA = 'Media';
					
	const _PLAYLIST = 'Playlist';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastChoiceType extends SoapObject
{				
	const _DYNAMIC = 'Dynamic';
					
	const _PLACEHOLDER = 'Placeholder';
					
	const _STATIC = 'Static';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastRestrictionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _AVAILABLETIME = 'availableTime';
					
	const _AVAILABLETIMEUNITS = 'availableTimeUnits';
					
	const _DELIVERY = 'delivery';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRATIONTIME = 'expirationTime';
					
	const _EXPIRATIONTIMEUNITS = 'expirationTimeUnits';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LIMITTOENDUSERNAMES = 'limitToEndUserNames';
					
	const _LIMITTOEXTERNALGROUPS = 'limitToExternalGroups';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REQUIREDRM = 'requireDRM';
					
	const _RETENTIONDATE = 'retentionDate';
					
	const _RETENTIONTIME = 'retentionTime';
					
	const _RETENTIONTIMEUNITS = 'retentionTimeUnits';
					
	const _TITLE = 'title';
					
	const _UNAPPROVEDATE = 'unapproveDate';
					
	const _UNAPPROVETIME = 'unapproveTime';
					
	const _UNAPPROVETIMEUNITS = 'unapproveTimeUnits';
					
	const _USEAIRDATE = 'useAirdate';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastCategoryField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AVAILABLEONSHAREDCONTENT = 'availableOnSharedContent';
					
	const _DEFAULTLICENSEIDS = 'defaultLicenseIDs';
					
	const _DEFAULTLICENSES = 'defaultLicenses';
					
	const _DEFAULTRESTRICTIONIDS = 'defaultRestrictionIDs';
					
	const _DEFAULTRESTRICTIONS = 'defaultRestrictions';
					
	const _DEFAULTUSAGEPLANIDS = 'defaultUsagePlanIDs';
					
	const _DEFAULTUSAGEPLANS = 'defaultUsagePlans';
					
	const _DEPTH = 'depth';
					
	const _DESCRIPTION = 'description';
					
	const _FULLTITLE = 'fullTitle';
					
	const _GUID = 'guid';
					
	const _HASCHILDREN = 'hasChildren';
					
	const _INDEX = 'index';
					
	const _LABEL = 'label';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MEDIAIDS = 'mediaIDs';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARENTCATEGORY = 'parentCategory';
					
	const _PARENTCATEGORYID = 'parentCategoryId';
					
	const _PLAYLISTIDS = 'playlistIDs';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _SCHEME = 'scheme';
					
	const _SHOWINPICKER = 'showInPicker';
					
	const _SHOWINPORTALS = 'showInPortals';
					
	const _SORTBYPLAYLIST = 'sortByPlaylist';
					
	const _SORTBYPLAYLISTID = 'sortByPlaylistID';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TREEORDER = 'treeOrder';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastChoiceField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWGLOBALCONTENT = 'allowGlobalContent';
					
	const _ALLOWHIGHERBITRATES = 'allowHigherBitrates';
					
	const _CATEGORIES = 'categories';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _CHOICELIMIT = 'choiceLimit';
					
	const _CONTENTADDED = 'contentAdded';
					
	const _CONTENTAIRDATE = 'contentAirdate';
					
	const _CONTENTALLUSAGEPLANIDS = 'contentAllUsagePlanIDs';
					
	const _CONTENTALLUSAGEPLANS = 'contentAllUsagePlans';
					
	const _CONTENTAPPROVED = 'contentApproved';
					
	const _CONTENTAUTHOR = 'contentAuthor';
					
	const _CONTENTBANNER = 'contentBanner';
					
	const _CONTENTCATEGORIES = 'contentCategories';
					
	const _CONTENTCATEGORYIDS = 'contentCategoryIDs';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTCONTENTTYPE = 'contentContentType';
					
	const _CONTENTCOPYRIGHT = 'contentCopyright';
					
	const _CONTENTDESCRIPTION = 'contentDescription';
					
	const _CONTENTEXCLUDETARGETLOCATIONS = 'contentExcludeTargetLocations';
					
	const _CONTENTFORMATS = 'contentFormats';
					
	const _CONTENTHASLICENSES = 'contentHasLicenses';
					
	const _CONTENTHASRESTRICTIONS = 'contentHasRestrictions';
					
	const _CONTENTHASTRANSCRIPT = 'contentHasTranscript';
					
	const _CONTENTHASUSAGEPLANS = 'contentHasUsagePlans';
					
	const _CONTENTHIGHESTBITRATE = 'contentHighestBitrate';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTKEYWORDS = 'contentKeywords';
					
	const _CONTENTLANGUAGE = 'contentLanguage';
					
	const _CONTENTLASTMODIFIED = 'contentLastModified';
					
	const _CONTENTLENGTH = 'contentLength';
					
	const _CONTENTLOWESTBITRATE = 'contentLowestBitrate';
					
	const _CONTENTMOREINFO = 'contentMoreInfo';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTPID = 'contentPID';
					
	const _CONTENTPOSSIBLERELEASESETTINGS = 'contentPossibleReleaseSettings';
					
	const _CONTENTRATING = 'contentRating';
					
	const _CONTENTSTATUS = 'contentStatus';
					
	const _CONTENTSTATUSDETAIL = 'contentStatusDetail';
					
	const _CONTENTTARGETCOUNTRIES = 'contentTargetCountries';
					
	const _CONTENTTARGETREGIONS = 'contentTargetRegions';
					
	const _CONTENTTHUMBNAILURL = 'contentThumbnailURL';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _CONTENTTRANSCRIPT = 'contentTranscript';
					
	const _CONTENTTRANSCRIPTURL = 'contentTranscriptURL';
					
	const _CONTENTTYPE = 'contentType';
					
	const _DESCRIPTION = 'description';
					
	const _INDEX = 'index';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MATCHTARGETLOCATION = 'matchTargetLocation';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PLAYPERCENTAGE = 'playPercentage';
					
	const _PLAYLISTID = 'playlistID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _SKIPNEXTCHOICEIFMATCH = 'skipNextChoiceIfMatch';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TAGS = 'tags';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastMediaField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _ACTUALAPPROVED = 'actualApproved';
					
	const _ACTUALAVAILABLEDATE = 'actualAvailableDate';
					
	const _ACTUALEXPIRATIONDATE = 'actualExpirationDate';
					
	const _ACTUALRETENTIONDATE = 'actualRetentionDate';
					
	const _ACTUALRETENTIONTIME = 'actualRetentionTime';
					
	const _ACTUALRETENTIONTIMEUNITS = 'actualRetentionTimeUnits';
					
	const _ACTUALUNAPPROVEDATE = 'actualUnapproveDate';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATE = 'airdate';
					
	const _ALBUM = 'album';
					
	const _ALLLICENSEIDS = 'allLicenseIDs';
					
	const _ALLLICENSES = 'allLicenses';
					
	const _ALLRESTRICTIONIDS = 'allRestrictionIDs';
					
	const _ALLRESTRICTIONS = 'allRestrictions';
					
	const _ALLUSAGEPLANIDS = 'allUsagePlanIDs';
					
	const _ALLUSAGEPLANS = 'allUsagePlans';
					
	const _ALLOWFASTFORWARDING = 'allowFastForwarding';
					
	const _APPLYINHERITEDRESTRICTIONS = 'applyInheritedRestrictions';
					
	const _APPROVED = 'approved';
					
	const _AUTHOR = 'author';
					
	const _AVAILABLE = 'available';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _BANNER = 'banner';
					
	const _CANDELETE = 'canDelete';
					
	const _CATEGORIES = 'categories';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _CHOICEIDS = 'choiceIDs';
					
	const _CONTAINERPLAYLISTIDS = 'containerPlaylistIDs';
					
	const _CONTAINERPLAYLISTS = 'containerPlaylists';
					
	const _CONTENTTYPE = 'contentType';
					
	const _COPYRIGHT = 'copyright';
					
	const _DESCRIPTION = 'description';
					
	const _EXCLUDETARGETLOCATIONS = 'excludeTargetLocations';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRED = 'expired';
					
	const _EXTERNALID = 'externalID';
					
	const _FORMATS = 'formats';
					
	const _GENRE = 'genre';
					
	const _HASAVAILABLERELEASES = 'hasAvailableReleases';
					
	const _HASLICENSES = 'hasLicenses';
					
	const _HASRESTRICTIONS = 'hasRestrictions';
					
	const _HASTRANSCRIPT = 'hasTranscript';
					
	const _HASUSAGEPLANS = 'hasUsagePlans';
					
	const _HIGHESTBITRATE = 'highestBitrate';
					
	const _KEYWORDS = 'keywords';
					
	const _LANGUAGE = 'language';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LICENSES = 'licenses';
					
	const _LOCKED = 'locked';
					
	const _LOWESTBITRATE = 'lowestBitrate';
					
	const _MEDIAFILECOUNT = 'mediaFileCount';
					
	const _MEDIAFILEIDS = 'mediaFileIDs';
					
	const _MOREINFO = 'moreInfo';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _POSSIBLERELEASESETTINGS = 'possibleReleaseSettings';
					
	const _RATING = 'rating';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _RELEASECOUNT = 'releaseCount';
					
	const _RELEASEIDS = 'releaseIDs';
					
	const _RESTRICTIONIDS = 'restrictionIDs';
					
	const _RESTRICTIONS = 'restrictions';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TARGETCOUNTRIES = 'targetCountries';
					
	const _TARGETREGIONS = 'targetRegions';
					
	const _THUMBNAILMEDIAFILEID = 'thumbnailMediaFileID';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TRANSCRIPT = 'transcript';
					
	const _TRANSCRIPTURL = 'transcriptURL';
					
	const _UNAPPROVEDATE = 'unapproveDate';
					
	const _USAGEPLANIDS = 'usagePlanIDs';
					
	const _USAGEPLANS = 'usagePlans';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastMediaFileField extends SoapObject
{				
	const _ID = 'ID';
					
	const _URL = 'URL';
					
	const _ACTUALRETENTIONDATE = 'actualRetentionDate';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWRELEASE = 'allowRelease';
					
	const _APPROVED = 'approved';
					
	const _ASSETTYPEIDS = 'assetTypeIDs';
					
	const _ASSETTYPES = 'assetTypes';
					
	const _AUDIOCHANNELS = 'audioChannels';
					
	const _AUDIOCODEC = 'audioCodec';
					
	const _AUDIOSAMPLERATE = 'audioSampleRate';
					
	const _AUDIOSAMPLESIZE = 'audioSampleSize';
					
	const _BACKUPSTREAMINGURL = 'backupStreamingURL';
					
	const _BITRATE = 'bitrate';
					
	const _CACHENEWFILE = 'cacheNewFile';
					
	const _CACHED = 'cached';
					
	const _CANDELETE = 'canDelete';
					
	const _CHECKSUM = 'checksum';
					
	const _CHECKSUMALGORITHM = 'checksumAlgorithm';
					
	const _CONTENT = 'content';
					
	const _CONTENTTYPE = 'contentType';
					
	const _CUSTOMFILEPATH = 'customFilePath';
					
	const _DELETEDDATE = 'deletedDate';
					
	const _DESCRIPTION = 'description';
					
	const _DRMKEYID = 'drmKeyID';
					
	const _DYNAMIC = 'dynamic';
					
	const _ENCODENEW = 'encodeNew';
					
	const _ENCODINGPROFILEID = 'encodingProfileID';
					
	const _ENCODINGPROFILETITLE = 'encodingProfileTitle';
					
	const _EXPRESSION = 'expression';
					
	const _FORMAT = 'format';
					
	const _FRAMERATE = 'frameRate';
					
	const _GUID = 'guid';
					
	const _HEIGHT = 'height';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _ISDEFAULT = 'isDefault';
					
	const _ISTHUMBNAIL = 'isThumbnail';
					
	const _LANGUAGE = 'language';
					
	const _LASTCACHED = 'lastCached';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LOCATIONID = 'locationID';
					
	const _LOCKED = 'locked';
					
	const _MEDIAFILETYPE = 'mediaFileType';
					
	const _MEDIAID = 'mediaID';
					
	const _ORIGINALLOCATION = 'originalLocation';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARENTDRMKEYID = 'parentDRMKeyID';
					
	const _PROTECTEDWITHDRM = 'protectedWithDRM';
					
	const _PROTECTIONSCHEME = 'protectionScheme';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREDFILENAME = 'requiredFileName';
					
	const _SIZE = 'size';
					
	const _SOURCEMEDIAFILEID = 'sourceMediaFileID';
					
	const _SOURCETIME = 'sourceTime';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STORAGE = 'storage';
					
	const _STORAGESERVERID = 'storageServerID';
					
	const _STORAGESERVERICON = 'storageServerIcon';
					
	const _STOREDFILENAME = 'storedFileName';
					
	const _STOREDFILEPATH = 'storedFilePath';
					
	const _STREAMINGURL = 'streamingURL';
					
	const _SYSTEMTASKID = 'systemTaskID';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TRUEFORMAT = 'trueFormat';
					
	const _UNDELETE = 'undelete';
					
	const _USEDASMEDIATHUMBNAIL = 'usedAsMediaThumbnail';
					
	const _VERIFY = 'verify';
					
	const _VERSION = 'version';
					
	const _VIDEOCODEC = 'videoCodec';
					
	const _WIDTH = 'width';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastPlaylistField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _ACTUALAPPROVED = 'actualApproved';
					
	const _ACTUALAVAILABLEDATE = 'actualAvailableDate';
					
	const _ACTUALEXPIRATIONDATE = 'actualExpirationDate';
					
	const _ACTUALRETENTIONDATE = 'actualRetentionDate';
					
	const _ACTUALRETENTIONTIME = 'actualRetentionTime';
					
	const _ACTUALRETENTIONTIMEUNITS = 'actualRetentionTimeUnits';
					
	const _ACTUALUNAPPROVEDATE = 'actualUnapproveDate';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATE = 'airdate';
					
	const _ALLLICENSEIDS = 'allLicenseIDs';
					
	const _ALLLICENSES = 'allLicenses';
					
	const _ALLRESTRICTIONIDS = 'allRestrictionIDs';
					
	const _ALLRESTRICTIONS = 'allRestrictions';
					
	const _ALLUSAGEPLANIDS = 'allUsagePlanIDs';
					
	const _ALLUSAGEPLANS = 'allUsagePlans';
					
	const _APPLYINHERITEDRESTRICTIONS = 'applyInheritedRestrictions';
					
	const _APPROVED = 'approved';
					
	const _AUTHOR = 'author';
					
	const _AVAILABLE = 'available';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _BANNER = 'banner';
					
	const _CATEGORIES = 'categories';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _CHOICECOUNT = 'choiceCount';
					
	const _CHOICEIDS = 'choiceIDs';
					
	const _CONTAINERPLAYLISTIDS = 'containerPlaylistIDs';
					
	const _CONTAINERPLAYLISTS = 'containerPlaylists';
					
	const _CONTENTTYPE = 'contentType';
					
	const _COPYRIGHT = 'copyright';
					
	const _DESCRIPTION = 'description';
					
	const _EXCLUDETARGETLOCATIONS = 'excludeTargetLocations';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRED = 'expired';
					
	const _EXTERNALID = 'externalID';
					
	const _FORMATS = 'formats';
					
	const _HASAVAILABLERELEASES = 'hasAvailableReleases';
					
	const _HASLICENSES = 'hasLicenses';
					
	const _HASRESTRICTIONS = 'hasRestrictions';
					
	const _HASTRANSCRIPT = 'hasTranscript';
					
	const _HASUSAGEPLANS = 'hasUsagePlans';
					
	const _HIGHESTBITRATE = 'highestBitrate';
					
	const _KEYWORDS = 'keywords';
					
	const _LANGUAGE = 'language';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LICENSES = 'licenses';
					
	const _LOCKED = 'locked';
					
	const _LOWESTBITRATE = 'lowestBitrate';
					
	const _MOREINFO = 'moreInfo';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _POSSIBLERELEASESETTINGS = 'possibleReleaseSettings';
					
	const _RATING = 'rating';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _RELEASECOUNT = 'releaseCount';
					
	const _RELEASEIDS = 'releaseIDs';
					
	const _RESTRICTIONIDS = 'restrictionIDs';
					
	const _RESTRICTIONS = 'restrictions';
					
	const _SHUFFLEPLAY = 'shufflePlay';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TARGETCOUNTRIES = 'targetCountries';
					
	const _TARGETREGIONS = 'targetRegions';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TRANSCRIPT = 'transcript';
					
	const _TRANSCRIPTURL = 'transcriptURL';
					
	const _UNAPPROVEDATE = 'unapproveDate';
					
	const _USAGEPLANIDS = 'usagePlanIDs';
					
	const _USAGEPLANS = 'usagePlans';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastReleaseField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _URL = 'URL';
					
	const _ACTUALAPPROVED = 'actualApproved';
					
	const _ACTUALAVAILABLEDATE = 'actualAvailableDate';
					
	const _ACTUALEXPIRATIONDATE = 'actualExpirationDate';
					
	const _ACTUALLIMITTOENDUSERNAMES = 'actualLimitToEndUserNames';
					
	const _ACTUALLIMITTOEXTERNALGROUPS = 'actualLimitToExternalGroups';
					
	const _ACTUALUNAPPROVEDATE = 'actualUnapproveDate';
					
	const _ADPOLICYID = 'adPolicyId';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLIEDCONTAINERPLAYLISTIDS = 'appliedContainerPlaylistIDs';
					
	const _APPLIEDCONTAINERPLAYLISTS = 'appliedContainerPlaylists';
					
	const _APPLIEDLICENSEIDS = 'appliedLicenseIDs';
					
	const _APPLIEDLICENSES = 'appliedLicenses';
					
	const _APPLIEDPARENTLICENSE = 'appliedParentLicense';
					
	const _APPLIEDPARENTLICENSEID = 'appliedParentLicenseID';
					
	const _APPLIEDRESTRICTIONIDS = 'appliedRestrictionIDs';
					
	const _APPLIEDRESTRICTIONS = 'appliedRestrictions';
					
	const _APPLYINHERITEDCONTAINERPLAYLISTS = 'applyInheritedContainerPlaylists';
					
	const _APPLYINHERITEDLICENSES = 'applyInheritedLicenses';
					
	const _APPROVED = 'approved';
					
	const _ASSETTYPE = 'assetType';
					
	const _ASSETTYPEID = 'assetTypeID';
					
	const _BACKUPMEDIAFILEURLS = 'backupMediaFileURLs';
					
	const _BITRATE = 'bitrate';
					
	const _CHECKSUMS = 'checksums';
					
	const _CONTAINERPLAYLISTIDS = 'containerPlaylistIDs';
					
	const _CONTAINERPLAYLISTS = 'containerPlaylists';
					
	const _CONTENTADDED = 'contentAdded';
					
	const _CONTENTAIRDATE = 'contentAirdate';
					
	const _CONTENTALLUSAGEPLANIDS = 'contentAllUsagePlanIDs';
					
	const _CONTENTALLUSAGEPLANS = 'contentAllUsagePlans';
					
	const _CONTENTAPPROVED = 'contentApproved';
					
	const _CONTENTAUTHOR = 'contentAuthor';
					
	const _CONTENTBANNER = 'contentBanner';
					
	const _CONTENTCATEGORIES = 'contentCategories';
					
	const _CONTENTCATEGORYIDS = 'contentCategoryIDs';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTCONTENTTYPE = 'contentContentType';
					
	const _CONTENTCOPYRIGHT = 'contentCopyright';
					
	const _CONTENTDESCRIPTION = 'contentDescription';
					
	const _CONTENTEXCLUDETARGETLOCATIONS = 'contentExcludeTargetLocations';
					
	const _CONTENTFORMATS = 'contentFormats';
					
	const _CONTENTHASLICENSES = 'contentHasLicenses';
					
	const _CONTENTHASRESTRICTIONS = 'contentHasRestrictions';
					
	const _CONTENTHASTRANSCRIPT = 'contentHasTranscript';
					
	const _CONTENTHASUSAGEPLANS = 'contentHasUsagePlans';
					
	const _CONTENTHIGHESTBITRATE = 'contentHighestBitrate';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTKEYWORDS = 'contentKeywords';
					
	const _CONTENTLANGUAGE = 'contentLanguage';
					
	const _CONTENTLASTMODIFIED = 'contentLastModified';
					
	const _CONTENTLENGTH = 'contentLength';
					
	const _CONTENTLOWESTBITRATE = 'contentLowestBitrate';
					
	const _CONTENTMOREINFO = 'contentMoreInfo';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTPID = 'contentPID';
					
	const _CONTENTPOSSIBLERELEASESETTINGS = 'contentPossibleReleaseSettings';
					
	const _CONTENTRATING = 'contentRating';
					
	const _CONTENTSTATUS = 'contentStatus';
					
	const _CONTENTSTATUSDETAIL = 'contentStatusDetail';
					
	const _CONTENTTARGETCOUNTRIES = 'contentTargetCountries';
					
	const _CONTENTTARGETREGIONS = 'contentTargetRegions';
					
	const _CONTENTTHUMBNAILURL = 'contentThumbnailURL';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _CONTENTTRANSCRIPT = 'contentTranscript';
					
	const _CONTENTTRANSCRIPTURL = 'contentTranscriptURL';
					
	const _CONTENTTYPE = 'contentType';
					
	const _CUSTOMMEDIAFILEPATH = 'customMediaFilePath';
					
	const _DELAYEDPID = 'delayedPID';
					
	const _DELIVERY = 'delivery';
					
	const _DESCRIPTION = 'description';
					
	const _DRMKEYID = 'drmKeyID';
					
	const _ENCODINGPROFILEID = 'encodingProfileID';
					
	const _ENCODINGPROFILETITLE = 'encodingProfileTitle';
					
	const _EXTERNALID = 'externalID';
					
	const _FORMAT = 'format';
					
	const _GUID = 'guid';
					
	const _HASLICENSES = 'hasLicenses';
					
	const _HEIGHT = 'height';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LICENSEACQUISITIONURL = 'licenseAcquisitionURL';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LICENSES = 'licenses';
					
	const _LOCKED = 'locked';
					
	const _MEDIAFILEURLS = 'mediaFileURLs';
					
	const _MINIMUMDRMSDKVERSION = 'minimumDRMSDKVersion';
					
	const _NETWORK = 'network';
					
	const _NETWORKSERVERID = 'networkServerID';
					
	const _NETWORKSERVERICON = 'networkServerIcon';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARAMETERS = 'parameters';
					
	const _PROTECTIONLEVEL = 'protectionLevel';
					
	const _REFRESH = 'refresh';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREINDIVIDUALIZATION = 'requireIndividualization';
					
	const _REQUIREDMEDIAFILENAME = 'requiredMediaFileName';
					
	const _RESTRICTIONID = 'restrictionId';
					
	const _SETTINGS = 'settings';
					
	const _SIZE = 'size';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _SUPPORTEDBYFLASHPLAYER = 'supportedByFlashPlayer';
					
	const _SUPPORTEDBYQUICKTIMEPLAYER = 'supportedByQuickTimePlayer';
					
	const _SUPPORTEDBYREALMEDIAPLAYER = 'supportedByRealMediaPlayer';
					
	const _SUPPORTEDBYWINDOWSMEDIAPLAYER = 'supportedByWindowsMediaPlayer';
					
	const _SYSTEMTASKIDS = 'systemTaskIDs';
					
	const _TRUEFORMAT = 'trueFormat';
					
	const _UNAPPROVEPREVIOUSRELEASEONPIDCHANGE = 'unapprovePreviousReleaseOnPIDChange';
					
	const _VERSION = 'version';
					
	const _WIDTH = 'width';
					
	const _WMRMLICENSEKEYSEED = 'wmrmLicenseKeySeed';
					
	const _WMRMPRIVATEKEY = 'wmrmPrivateKey';
					
	const _WMRMPUBLICKEY = 'wmrmPublicKey';
					
	const _WMRMREVOCATIONPRIVATEKEY = 'wmrmRevocationPrivateKey';
					
	const _WMRMREVOCATIONPUBLICKEY = 'wmrmRevocationPublicKey';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastRequestField extends SoapObject
{				
	const _AFFILIATE = 'affiliate';
					
	const _ASSETTYPE = 'assetType';
					
	const _AUTHOR = 'author';
					
	const _BITRATE = 'bitrate';
					
	const _BITRATEINKBPS = 'bitrateInKbps';
					
	const _BROWSER = 'browser';
					
	const _BUFFERING = 'buffering';
					
	const _CATEGORIES = 'categories';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTIDFORGROUP = 'contentIDForGroup';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTTYPE = 'contentType';
					
	const _COUNTRY = 'country';
					
	const _DELIVERY = 'delivery';
					
	const _ENCODINGPROFILE = 'encodingProfile';
					
	const _FORMAT = 'format';
					
	const _INPLAYLIST = 'inPlaylist';
					
	const _INPLAYLISTID = 'inPlaylistID';
					
	const _INPLAYLISTIDFORGROUP = 'inPlaylistIDForGroup';
					
	const _LANGUAGE = 'language';
					
	const _LENGTH = 'length';
					
	const _LENGTHPLAYED = 'lengthPlayed';
					
	const _LOADTIME = 'loadTime';
					
	const _NETWORK = 'network';
					
	const _NETWORKSERVERID = 'networkServerID';
					
	const _OPERATINGSYSTEM = 'operatingSystem';
					
	const _OUTLET = 'outlet';
					
	const _OUTLETACCOUNTID = 'outletAccountID';
					
	const _PLAYED = 'played';
					
	const _PLAYER = 'player';
					
	const _PORTAL = 'portal';
					
	const _QUALITY = 'quality';
					
	const _RATING = 'rating';
					
	const _REGION = 'region';
					
	const _REQUESTCOUNT = 'requestCount';
					
	const _REQUESTDATE = 'requestDate';
					
	const _REQUESTDATEONLY = 'requestDateOnly';
					
	const _REQUESTDAYOFWEEK = 'requestDayOfWeek';
					
	const _REQUESTHOUR = 'requestHour';
					
	const _REQUESTMONTH = 'requestMonth';
					
	const _REQUESTMONTHONLY = 'requestMonthOnly';
					
	const _SIZE = 'size';
					
	const _TITLE = 'title';
					
	const _TRACKINGCOUNT = 'trackingCount';
					
	const _USERNAME = 'userName';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastBitrateMode extends SoapObject
{				
	const _CONSTANT = 'Constant';
					
	const _VARIABLEWITHMAXIMUM = 'VariableWithMaximum';
					
	const _VARIABLEWITHOUTMAXIMUM = 'VariableWithoutMaximum';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEncodingProvider extends SoapObject
{				
	const _COMMANDLINE = 'CommandLine';
					
	const _DIGITALRAPIDS = 'DigitalRapids';
					
	const _FLASHACCESS = 'FlashAccess';
					
	const _FLASHDYNAMIC = 'FlashDynamic';
					
	const _FLIPFACTORY = 'FlipFactory';
					
	const _FLIPFACTORY5 = 'FlipFactory5';
					
	const _FLIPFACTORY6 = 'FlipFactory6';
					
	const _FLIXENGINE8 = 'FlixEngine8';
					
	const _IISTRANSFORMMANAGER = 'IISTransformManager';
					
	const _MOVENETWORKS = 'MoveNetworks';
					
	const _NONE = 'None';
					
	const _RADIANTGRID = 'RadiantGrid';
					
	const _RHOZET = 'Rhozet';
					
	const _SCENECAST = 'Scenecast';
					
	const _THEPLATFORM = 'thePlatform';
					
	const _WIDEVINE = 'Widevine';
					
	const _WM9 = 'WM9';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastHinting extends SoapObject
{				
	const _NONE = 'None';
					
	const _OPTIMIZEFORSIZE = 'OptimizeForSize';
					
	const _OPTIMIZEFORSPEED = 'OptimizeForSpeed';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEncodingProfileField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUDIOBITRATE = 'audioBitrate';
					
	const _AUDIOBITRATEMODE = 'audioBitrateMode';
					
	const _AUDIOBITSPERSAMPLE = 'audioBitsPerSample';
					
	const _AUDIOCHANNELS = 'audioChannels';
					
	const _AUDIOCODECID = 'audioCodecID';
					
	const _AUDIOCODECTITLE = 'audioCodecTitle';
					
	const _AUDIOSAMPLERATE = 'audioSampleRate';
					
	const _AVAILABLEONSHAREDCONTENT = 'availableOnSharedContent';
					
	const _CONTENTTYPE = 'contentType';
					
	const _CORRECTFORREPEATEDFRAMES = 'correctForRepeatedFrames';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENCODINGPROVIDER = 'encodingProvider';
					
	const _EXTERNALENCODINGPROFILEID = 'externalEncodingProfileID';
					
	const _FILEEXTENSION = 'fileExtension';
					
	const _FORMAT = 'format';
					
	const _HINTING = 'hinting';
					
	const _IMAGEHEIGHT = 'imageHeight';
					
	const _IMAGEQUALITY = 'imageQuality';
					
	const _IMAGEWIDTH = 'imageWidth';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MAXIMUMAUDIOBITRATE = 'maximumAudioBitrate';
					
	const _MAXIMUMAUDIOBUFFERING = 'maximumAudioBuffering';
					
	const _MAXIMUMPACKETDURATION = 'maximumPacketDuration';
					
	const _MAXIMUMPACKETSIZE = 'maximumPacketSize';
					
	const _MAXIMUMVIDEOBITRATE = 'maximumVideoBitrate';
					
	const _MAXIMUMVIDEOBUFFERING = 'maximumVideoBuffering';
					
	const _OPTIMIZEFORENCODINGSPEED = 'optimizeForEncodingSpeed';
					
	const _OPTIMIZEFORPORTABLEDEVICES = 'optimizeForPortableDevices';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TITLE = 'title';
					
	const _TOTALBITRATE = 'totalBitrate';
					
	const _VERSION = 'version';
					
	const _VIDEOBITRATE = 'videoBitrate';
					
	const _VIDEOBITRATEMODE = 'videoBitrateMode';
					
	const _VIDEOCODECID = 'videoCodecID';
					
	const _VIDEOCODECTITLE = 'videoCodecTitle';
					
	const _VIDEOFRAMERATE = 'videoFrameRate';
					
	const _VIDEOKEYFRAMEINTERVAL = 'videoKeyFrameInterval';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAssetTypeField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLYBYDEFAULT = 'applyByDefault';
					
	const _APPLYTOTHUMBNAILSBYDEFAULT = 'applyToThumbnailsByDefault';
					
	const _DESCRIPTION = 'description';
					
	const _GUID = 'guid';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHAREWITHACCOUNTIDS = 'shareWithAccountIDs';
					
	const _SHAREWITHACCOUNTS = 'shareWithAccounts';
					
	const _SHAREWITHALLACCOUNTS = 'shareWithAllAccounts';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastRestrictionTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfRestrictionField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRestrictionField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastCategoryTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfCategoryField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfCategoryField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastChoiceTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfChoiceField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			case 'contentCustomFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfChoiceField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentCustomFields;
				
}
	
class ComcastMediaTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfMediaField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfMediaField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastMediaFileTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfMediaFileField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfMediaFileField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastPlaylistTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfPlaylistField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPlaylistField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastReleaseTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfReleaseField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			case 'contentCustomFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfReleaseField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $contentCustomFields;
				
}
	
class ComcastRequestTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfRequestField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfRequestField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastEncodingProfileTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfEncodingProfileField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfEncodingProfileField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastAssetTypeTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfAssetTypeField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfAssetTypeField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastRestrictionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastRestrictionField';
			case 'tieBreaker':
				return 'ComcastRestrictionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastRestrictionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastRestrictionSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastCategorySort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastCategoryField';
			case 'tieBreaker':
				return 'ComcastCategorySort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastCategoryField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastCategorySort
	 **/
	public $tieBreaker;
				
}
	
class ComcastChoiceSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastChoiceField';
			case 'tieBreaker':
				return 'ComcastChoiceSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastChoiceField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastChoiceSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastMediaSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastMediaField';
			case 'tieBreaker':
				return 'ComcastMediaSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastMediaField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastMediaSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastMediaFileSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastMediaFileField';
			case 'tieBreaker':
				return 'ComcastMediaFileSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastMediaFileField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastMediaFileSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastPlaylistSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastPlaylistField';
			case 'tieBreaker':
				return 'ComcastPlaylistSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastPlaylistField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastPlaylistSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastReleaseSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastReleaseField';
			case 'tieBreaker':
				return 'ComcastReleaseSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastReleaseField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastReleaseSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastRequestSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastRequestField';
			case 'tieBreaker':
				return 'ComcastRequestSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastRequestField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastRequestSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastEncodingProfileSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEncodingProfileField';
			case 'tieBreaker':
				return 'ComcastEncodingProfileSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastEncodingProfileField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEncodingProfileSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastAssetTypeSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastAssetTypeField';
			case 'tieBreaker':
				return 'ComcastAssetTypeSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastAssetTypeField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastAssetTypeSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastEndUserList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUser");	
	}
					
}
	
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
	
class ComcastEndUserPermissionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermission");	
	}
					
}
	
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
	
class ComcastLicenseList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLicense");	
	}
					
}
	
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
	
class ComcastPortalList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPortal");	
	}
					
}
	
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
	
class ComcastStorefrontPageList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPage");	
	}
					
}
	
class ComcastStorefrontPage extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfStorefrontPageField';
			case 'licenseIDs':
				return 'ComcastIDSet';
			case 'licenses':
				return 'ComcastArrayOfstring';
			case 'limitToAuthors':
				return 'ComcastArrayOfstring';
			case 'limitToCategories':
				return 'ComcastArrayOfstring';
			case 'limitToCategoryIDs':
				return 'ComcastIDSet';
			case 'searchCategories':
				return 'ComcastArrayOfstring';
			case 'searchCategoryIDs':
				return 'ComcastIDSet';
			case 'storefrontPageType':
				return 'ComcastStorefrontPageType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfStorefrontPageField
	 **/
	public $template;
				
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
	public $allowKeywordSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowTitleSearching;
				
	/**
	 * @var boolean
	 **/
	public $allowTranscriptSearching;
				
	/**
	 * @var string
	 **/
	public $authorLabel;
				
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
	public $customPageURL;
				
	/**
	 * @var string
	 **/
	public $descriptionLabel;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var int
	 **/
	public $headerHeight;
				
	/**
	 * @var int
	 **/
	public $index;
				
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
	 * @var ComcastIDSet
	 **/
	public $licenseIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $licenses;
				
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
	 * @var long
	 **/
	public $portalID;
				
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
	public $showAirdate;
				
	/**
	 * @var boolean
	 **/
	public $showAuthor;
				
	/**
	 * @var boolean
	 **/
	public $showGlobalContent;
				
	/**
	 * @var boolean
	 **/
	public $sortDescending;
				
	/**
	 * @var string
	 **/
	public $sortKey;
				
	/**
	 * @var long
	 **/
	public $storefrontID;
				
	/**
	 * @var ComcastStorefrontPageType
	 **/
	public $storefrontPageType;
				
	/**
	 * @var string
	 **/
	public $stylesheetURL;
				
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
	 * @var string
	 **/
	public $transcriptLabel;
				
	/**
	 * @var boolean
	 **/
	public $useExistingLicenses;
				
}
	
class ComcastStorefrontList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefront");	
	}
					
}
	
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
	
class ComcastUsagePlanList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlan");	
	}
					
}
	
class ComcastUsagePlan extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfUsagePlanField';
			case 'allowedAccountIDs':
				return 'ComcastIDSet';
			case 'allowedAccountNames':
				return 'ComcastArrayOfstring';
			case 'categoryIDs':
				return 'ComcastIDSet';
			case 'mediaIDs':
				return 'ComcastIDSet';
			case 'playlistIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfUsagePlanField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $allowBrowsing;
				
	/**
	 * @var boolean
	 **/
	public $allowCustomServerReleases;
				
	/**
	 * @var boolean
	 **/
	public $allowDownloads;
				
	/**
	 * @var boolean
	 **/
	public $allowPushing;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $allowedAccountIDs;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $allowedAccountNames;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $categoryIDs;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $mediaIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $playlistIDs;
				
	/**
	 * @var string
	 **/
	public $title;
				
}
	
class ComcastEndUserTransactionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransaction");	
	}
					
}
	
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
	
class ComcastPriceList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPrice");	
	}
					
}
	
class ComcastPrice extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfPriceField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPriceField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $couponCode;
				
	/**
	 * @var float
	 **/
	public $initialPrice;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var long
	 **/
	public $periodsPerRenewalCharge;
				
	/**
	 * @var boolean
	 **/
	public $preventDirectUse;
				
	/**
	 * @var float
	 **/
	public $pricePerLicense;
				
	/**
	 * @var long
	 **/
	public $renewalChargesAtSpecialPrice;
				
	/**
	 * @var float
	 **/
	public $renewalPrice;
				
	/**
	 * @var float
	 **/
	public $specialRenewalPrice;
				
}
	
class ComcastWMRMSignatureKeys extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $licenseServerCertificate;
				
	/**
	 * @var string
	 **/
	public $rootCertificate;
				
	/**
	 * @var string
	 **/
	public $privateKey;
				
	/**
	 * @var string
	 **/
	public $signedPublicKey;
				
}
	
class ComcastDRMChallengeState extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'licenseStates':
				return 'ComcastArrayOfDRMLicenseState';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $individualization;
				
	/**
	 * @var long
	 **/
	public $releaseID;
				
	/**
	 * @var string
	 **/
	public $releasePID;
				
	/**
	 * @var ComcastArrayOfDRMLicenseState
	 **/
	public $licenseStates;
				
}
	
class ComcastDRMLicenseState extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'expired':
				return 'Comcastboolean';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var Comcastboolean
	 **/
	public $expired;
				
	/**
	 * @var string
	 **/
	public $keyID;
				
}
	
class ComcastArrayOfDRMLicenseState extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDRMLicenseState");	
	}
					
}
	
class ComcastDRMRevocationOptions extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'releaseIDs':
				return 'ComcastIDSet';
			case 'parentLicenseIDs':
				return 'ComcastIDSet';
			case 'endUserIDs':
				return 'ComcastIDSet';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var string
	 **/
	public $challenge;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $releaseIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $parentLicenseIDs;
				
	/**
	 * @var ComcastIDSet
	 **/
	public $endUserIDs;
				
}
	
class ComcastArrayOfStorefrontOrder extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontOrder");	
	}
					
}
	
class ComcastEndUserTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfEndUserField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfEndUserField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastEndUserPermissionTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfEndUserPermissionField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfEndUserPermissionField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastLicenseTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfLicenseField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfLicenseField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastPortalTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfPortalField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPortalField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastStorefrontPageTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfStorefrontPageField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfStorefrontPageField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastStorefrontTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfStorefrontField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfStorefrontField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastUsagePlanTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfUsagePlanField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfUsagePlanField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastEndUserTransactionTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfEndUserTransactionField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfEndUserTransactionField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastPriceTemplate extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'fields':
				return 'ComcastArrayOfPriceField';
			case 'customFields':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfPriceField
	 **/
	public $fields;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $customFields;
				
}
	
class ComcastArrayOfEndUserField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserField");	
	}
					
}
	
class ComcastArrayOfEndUserPermissionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermissionField");	
	}
					
}
	
class ComcastArrayOfLicenseField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLicenseField");	
	}
					
}
	
class ComcastArrayOfPortalField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPortalField");	
	}
					
}
	
class ComcastArrayOfStorefrontPageField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPageField");	
	}
					
}
	
class ComcastArrayOfStorefrontField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontField");	
	}
					
}
	
class ComcastArrayOfUsagePlanField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlanField");	
	}
					
}
	
class ComcastArrayOfEndUserTransactionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserTransactionField");	
	}
					
}
	
class ComcastArrayOfPriceField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPriceField");	
	}
					
}
	
class ComcastStorefrontOrder extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'orderType':
				return 'ComcastStorefrontOrderType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastStorefrontOrderType
	 **/
	public $orderType;
				
	/**
	 * @var long
	 **/
	public $storefrontPageID;
				
	/**
	 * @var long
	 **/
	public $itemID;
				
	/**
	 * @var long
	 **/
	public $licenseID;
				
	/**
	 * @var boolean
	 **/
	public $copyLicense;
				
	/**
	 * @var boolean
	 **/
	public $prepaid;
				
	/**
	 * @var string
	 **/
	public $subscriptionID;
				
	/**
	 * @var string
	 **/
	public $transactionID;
				
	/**
	 * @var float
	 **/
	public $salesTaxRate;
				
	/**
	 * @var string
	 **/
	public $couponCode;
				
}
	
class ComcastStorefrontOrderOptions extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'couponCodes':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $couponCodes;
				
	/**
	 * @var string
	 **/
	public $endUserIPAddress;
				
}
	
class ComcastEndUserField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACCEPTEDLICENSEAGREEMENT = 'acceptedLicenseAgreement';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ADDRESS = 'address';
					
	const _ALTERNATEPHONENUMBER = 'alternatePhoneNumber';
					
	const _AUTHENTICATIONMETHOD = 'authenticationMethod';
					
	const _CITY = 'city';
					
	const _COMPANY = 'company';
					
	const _COUNTRY = 'country';
					
	const _CREDITCARDEXPIRATIONMONTH = 'creditCardExpirationMonth';
					
	const _CREDITCARDEXPIRATIONYEAR = 'creditCardExpirationYear';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDNUMBER = 'creditCardNumber';
					
	const _CREDITCARDSTATUS = 'creditCardStatus';
					
	const _CREDITCARDTOKEN = 'creditCardToken';
					
	const _CREDITCARDTOKENGENERATED = 'creditCardTokenGenerated';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _DISPLAYNAME = 'displayName';
					
	const _EMAILADDRESS = 'emailAddress';
					
	const _ENDUSERPERMISSIONCOUNT = 'endUserPermissionCount';
					
	const _ENDUSERPERMISSIONIDS = 'endUserPermissionIDs';
					
	const _FIRSTNAME = 'firstName';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTNAME = 'lastName';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LOCKED = 'locked';
					
	const _NAMEONCREDITCARD = 'nameOnCreditCard';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PHONENUMBER = 'phoneNumber';
					
	const _POSTALCODE = 'postalCode';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _STATE = 'state';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TIMEZONE = 'timeZone';
					
	const _USERNAME = 'userName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEndUserPermissionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUTOMATICALLYRENEW = 'automaticallyRenew';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _COUPONCODE = 'couponCode';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _CURRENTPLAYS = 'currentPlays';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENDUSERID = 'endUserID';
					
	const _ENDUSERNAME = 'endUserName';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXTERNALID = 'externalID';
					
	const _GRANTDATE = 'grantDate';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTRENEWED = 'lastRenewed';
					
	const _LICENSEID = 'licenseID';
					
	const _LICENSEMEDIAIDS = 'licenseMediaIDs';
					
	const _LICENSEPLAYLISTIDS = 'licensePlaylistIDs';
					
	const _LICENSETITLE = 'licenseTitle';
					
	const _LICENSESGRANTED = 'licensesGranted';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PREPAID = 'prepaid';
					
	const _PRICEID = 'priceID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REMAININGLICENSES = 'remainingLicenses';
					
	const _REMAININGPLAYS = 'remainingPlays';
					
	const _RENEW = 'renew';
					
	const _RENEWABLE = 'renewable';
					
	const _RENEWALS = 'renewals';
					
	const _RETRYLASTPAYMENT = 'retryLastPayment';
					
	const _SALESTAXRATE = 'salesTaxRate';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTID = 'storefrontID';
					
	const _STOREFRONTTITLE = 'storefrontTitle';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
	const _TOTALPLAYS = 'totalPlays';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastAuthentication extends SoapObject
{				
	const _EXTERNAL = 'External';
					
	const _END_USER = 'End-user';
					
	const _NONE = 'None';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastLicenseField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLIESTO = 'appliesTo';
					
	const _AUTHENTICATION = 'authentication';
					
	const _AUTHENTICATIONURL = 'authenticationURL';
					
	const _AUTOMATICALLYRENEWBYDEFAULT = 'automaticallyRenewByDefault';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _DEFAULTINITIALPRICE = 'defaultInitialPrice';
					
	const _DESCRIPTION = 'description';
					
	const _DIRECTORIES = 'directories';
					
	const _DIRECTORYIDS = 'directoryIDs';
					
	const _DISABLEBACKUPS = 'disableBackups';
					
	const _DISABLEONCLOCKROLLBACK = 'disableOnClockRollback';
					
	const _DISABLEONPC = 'disableOnPC';
					
	const _DISABLED = 'disabled';
					
	const _DRMKEYID = 'drmKeyID';
					
	const _ENDUSERIDS = 'endUserIDs';
					
	const _ENDUSERNAMES = 'endUserNames';
					
	const _ENDUSERPERMISSIONCOUNT = 'endUserPermissionCount';
					
	const _ENDUSERPERMISSIONIDS = 'endUserPermissionIDs';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRATIONTIME = 'expirationTime';
					
	const _EXPIRATIONTIMEAFTERFIRSTUSE = 'expirationTimeAfterFirstUse';
					
	const _EXPIRATIONTIMEAFTERFIRSTUSEUNITS = 'expirationTimeAfterFirstUseUnits';
					
	const _EXPIRATIONTIMEUNITS = 'expirationTimeUnits';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _FORMATS = 'formats';
					
	const _HIGHESTBITRATE = 'highestBitrate';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LICENSESPERENDUSER = 'licensesPerEndUser';
					
	const _LOCKED = 'locked';
					
	const _LOWESTBITRATE = 'lowestBitrate';
					
	const _MAXIMUMBURNS = 'maximumBurns';
					
	const _MAXIMUMPLAYS = 'maximumPlays';
					
	const _MAXIMUMRENEWALS = 'maximumRenewals';
					
	const _MAXIMUMTRANSFERSTODEVICE = 'maximumTransfersToDevice';
					
	const _MEDIAIDS = 'mediaIDs';
					
	const _MINIMUMRENEWALS = 'minimumRenewals';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARENTLICENSE = 'parentLicense';
					
	const _PARENTLICENSEID = 'parentLicenseID';
					
	const _PLAYLISTIDS = 'playlistIDs';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREINDIVIDUALIZATION = 'requireIndividualization';
					
	const _REQUIRESECUREPLAYER = 'requireSecurePlayer';
					
	const _SHOWINPICKER = 'showInPicker';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _SUBSCRIPTIONGRACEPERIOD = 'subscriptionGracePeriod';
					
	const _SUBSCRIPTIONGRACEPERIODUNITS = 'subscriptionGracePeriodUnits';
					
	const _SUBSCRIPTIONTRIALPERIOD = 'subscriptionTrialPeriod';
					
	const _SUBSCRIPTIONTRIALPERIODUNITS = 'subscriptionTrialPeriodUnits';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
	const _TEMPLATELICENSETITLE = 'templateLicenseTitle';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TIMEALLOWED = 'timeAllowed';
					
	const _TIMEALLOWEDUNITS = 'timeAllowedUnits';
					
	const _TITLE = 'title';
					
	const _USEDRM = 'useDRM';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastPortalField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _RSSHASH = 'RSSHash';
					
	const _RSSLASTMODIFIED = 'RSSLastModified';
					
	const _RSSURL = 'RSSURL';
					
	const _URL = 'URL';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATEFORMAT = 'airdateFormat';
					
	const _AIRDATELABEL = 'airdateLabel';
					
	const _ALLOWAIRDATESEARCHING = 'allowAirdateSearching';
					
	const _ALLOWAPPROVAL = 'allowApproval';
					
	const _ALLOWAPPROVEDSEARCHING = 'allowApprovedSearching';
					
	const _ALLOWAUTHORSEARCHING = 'allowAuthorSearching';
					
	const _ALLOWCATEGORYSEARCHING = 'allowCategorySearching';
					
	const _ALLOWDESCRIPTIONSEARCHING = 'allowDescriptionSearching';
					
	const _ALLOWFULLSCREEN = 'allowFullScreen';
					
	const _ALLOWKEYWORDSEARCHING = 'allowKeywordSearching';
					
	const _ALLOWSELFEDITING = 'allowSelfEditing';
					
	const _ALLOWSELFREGISTRATION = 'allowSelfRegistration';
					
	const _ALLOWSIGNINRECOVERY = 'allowSignInRecovery';
					
	const _ALLOWSIGNOUT = 'allowSignOut';
					
	const _ALLOWTITLESEARCHING = 'allowTitleSearching';
					
	const _ALLOWTRANSCRIPTSEARCHING = 'allowTranscriptSearching';
					
	const _ALLOWUSERNAMEEDITING = 'allowUserNameEditing';
					
	const _ALTERNATEPHONENUMBERLABEL = 'alternatePhoneNumberLabel';
					
	const _AUTHORLABEL = 'authorLabel';
					
	const _AVAILABLEBITRATES = 'availableBitrates';
					
	const _AVAILABLEDELIVERY = 'availableDelivery';
					
	const _AVAILABLEFORMATS = 'availableFormats';
					
	const _BOTTOMFRAMEHEIGHT = 'bottomFrameHeight';
					
	const _BOTTOMFRAMEURL = 'bottomFrameURL';
					
	const _CATEGORYLABEL = 'categoryLabel';
					
	const _CUSTOMERSERVICEEMAILADDRESS = 'customerServiceEmailAddress';
					
	const _CUSTOMERSERVICEEMAILSIGNATURE = 'customerServiceEmailSignature';
					
	const _DEFAULTBITRATE = 'defaultBitrate';
					
	const _DEFAULTFORMAT = 'defaultFormat';
					
	const _DESCRIPTION = 'description';
					
	const _DESCRIPTIONLABEL = 'descriptionLabel';
					
	const _DISABLED = 'disabled';
					
	const _ENDUSERLICENSEAGREEMENT = 'endUserLicenseAgreement';
					
	const _EXCLUSIVEFORMATS = 'exclusiveFormats';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _HASENDUSERLICENSEAGREEMENT = 'hasEndUserLicenseAgreement';
					
	const _HEADERHEIGHT = 'headerHeight';
					
	const _ITEMSPERPAGE = 'itemsPerPage';
					
	const _KEYWORDSLABEL = 'keywordsLabel';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LEFTFRAMEURL = 'leftFrameURL';
					
	const _LEFTFRAMEWIDTH = 'leftFrameWidth';
					
	const _LIMITBYENDUSERLOCATION = 'limitByEndUserLocation';
					
	const _LIMITTOAUTHORS = 'limitToAuthors';
					
	const _LIMITTOCATEGORIES = 'limitToCategories';
					
	const _LIMITTOCATEGORYIDS = 'limitToCategoryIDs';
					
	const _LIMITTOPROTECTEDRELEASES = 'limitToProtectedReleases';
					
	const _LOCKED = 'locked';
					
	const _MINIMUMPASSWORDLENGTH = 'minimumPasswordLength';
					
	const _NEWWINDOWFORMATS = 'newWindowFormats';
					
	const _NEWWINDOWHEIGHT = 'newWindowHeight';
					
	const _NEWWINDOWWIDTH = 'newWindowWidth';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PHONENUMBERLABEL = 'phoneNumberLabel';
					
	const _PLAYERHEIGHT = 'playerHeight';
					
	const _PLAYERONLEFT = 'playerOnLeft';
					
	const _PLAYERSTRETCHTOFIT = 'playerStretchToFit';
					
	const _PLAYERURL = 'playerURL';
					
	const _PLAYERWIDTH = 'playerWidth';
					
	const _PROMPTFORPREFERENCES = 'promptForPreferences';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREADDRESS = 'requireAddress';
					
	const _REQUIREALTERNATEPHONENUMBER = 'requireAlternatePhoneNumber';
					
	const _REQUIRECITY = 'requireCity';
					
	const _REQUIRECOMPANY = 'requireCompany';
					
	const _REQUIRECOUNTRY = 'requireCountry';
					
	const _REQUIREEMAILADDRESS = 'requireEmailAddress';
					
	const _REQUIREFIRSTNAME = 'requireFirstName';
					
	const _REQUIRELASTNAME = 'requireLastName';
					
	const _REQUIREPASSWORD = 'requirePassword';
					
	const _REQUIREPHONENUMBER = 'requirePhoneNumber';
					
	const _REQUIREPOSTALCODE = 'requirePostalCode';
					
	const _REQUIRESIGNIN = 'requireSignIn';
					
	const _REQUIRESIGNINCONFIRMATION = 'requireSignInConfirmation';
					
	const _REQUIRESTATE = 'requireState';
					
	const _RIGHTFRAMEURL = 'rightFrameURL';
					
	const _RIGHTFRAMEWIDTH = 'rightFrameWidth';
					
	const _SEARCHCATEGORIES = 'searchCategories';
					
	const _SEARCHCATEGORYIDS = 'searchCategoryIDs';
					
	const _SENDSIGNINCONFIRMATION = 'sendSignInConfirmation';
					
	const _SHOWAIRDATE = 'showAirdate';
					
	const _SHOWAPPROVEDRELEASES = 'showApprovedReleases';
					
	const _SHOWAUTHOR = 'showAuthor';
					
	const _SHOWBITRATE = 'showBitrate';
					
	const _SHOWFORMAT = 'showFormat';
					
	const _SHOWGLOBALCONTENT = 'showGlobalContent';
					
	const _SHOWPLAYER = 'showPlayer';
					
	const _SHOWRELEASEURL = 'showReleaseURL';
					
	const _SHOWTRANSCRIPTBELOWPLAYER = 'showTranscriptBelowPlayer';
					
	const _SHOWUNAPPROVEDRELEASES = 'showUnapprovedReleases';
					
	const _SORTDESCENDING = 'sortDescending';
					
	const _SORTKEY = 'sortKey';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TITLELABEL = 'titleLabel';
					
	const _TOPFRAMEHEIGHT = 'topFrameHeight';
					
	const _TOPFRAMEURL = 'topFrameURL';
					
	const _TRACKBROWSER = 'trackBrowser';
					
	const _TRACKLOCATION = 'trackLocation';
					
	const _TRANSCRIPTLABEL = 'transcriptLabel';
					
	const _USEDIRECTORIES = 'useDirectories';
					
	const _USEEMAILADDRESSASUSERNAME = 'useEmailAddressAsUserName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStorefrontPageField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATEFORMAT = 'airdateFormat';
					
	const _AIRDATELABEL = 'airdateLabel';
					
	const _ALLOWAIRDATESEARCHING = 'allowAirdateSearching';
					
	const _ALLOWAUTHORSEARCHING = 'allowAuthorSearching';
					
	const _ALLOWCATEGORYSEARCHING = 'allowCategorySearching';
					
	const _ALLOWDESCRIPTIONSEARCHING = 'allowDescriptionSearching';
					
	const _ALLOWKEYWORDSEARCHING = 'allowKeywordSearching';
					
	const _ALLOWTITLESEARCHING = 'allowTitleSearching';
					
	const _ALLOWTRANSCRIPTSEARCHING = 'allowTranscriptSearching';
					
	const _AUTHORLABEL = 'authorLabel';
					
	const _BOTTOMFRAMEHEIGHT = 'bottomFrameHeight';
					
	const _BOTTOMFRAMEURL = 'bottomFrameURL';
					
	const _CATEGORYLABEL = 'categoryLabel';
					
	const _CUSTOMPAGEURL = 'customPageURL';
					
	const _DESCRIPTION = 'description';
					
	const _DESCRIPTIONLABEL = 'descriptionLabel';
					
	const _DISABLED = 'disabled';
					
	const _HEADERHEIGHT = 'headerHeight';
					
	const _INDEX = 'index';
					
	const _ITEMSPERPAGE = 'itemsPerPage';
					
	const _KEYWORDSLABEL = 'keywordsLabel';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LEFTFRAMEURL = 'leftFrameURL';
					
	const _LEFTFRAMEWIDTH = 'leftFrameWidth';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LICENSES = 'licenses';
					
	const _LIMITBYENDUSERLOCATION = 'limitByEndUserLocation';
					
	const _LIMITTOAUTHORS = 'limitToAuthors';
					
	const _LIMITTOCATEGORIES = 'limitToCategories';
					
	const _LIMITTOCATEGORYIDS = 'limitToCategoryIDs';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PORTALID = 'portalID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _RIGHTFRAMEURL = 'rightFrameURL';
					
	const _RIGHTFRAMEWIDTH = 'rightFrameWidth';
					
	const _SEARCHCATEGORIES = 'searchCategories';
					
	const _SEARCHCATEGORYIDS = 'searchCategoryIDs';
					
	const _SHOWAIRDATE = 'showAirdate';
					
	const _SHOWAUTHOR = 'showAuthor';
					
	const _SHOWGLOBALCONTENT = 'showGlobalContent';
					
	const _SORTDESCENDING = 'sortDescending';
					
	const _SORTKEY = 'sortKey';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTID = 'storefrontID';
					
	const _STOREFRONTPAGETYPE = 'storefrontPageType';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _TITLE = 'title';
					
	const _TITLELABEL = 'titleLabel';
					
	const _TOPFRAMEHEIGHT = 'topFrameHeight';
					
	const _TOPFRAMEURL = 'topFrameURL';
					
	const _TRANSCRIPTLABEL = 'transcriptLabel';
					
	const _USEEXISTINGLICENSES = 'useExistingLicenses';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStorefrontField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _URL = 'URL';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATEFORMAT = 'airdateFormat';
					
	const _ALLOWSELFEDITING = 'allowSelfEditing';
					
	const _ALLOWSELFREGISTRATION = 'allowSelfRegistration';
					
	const _ALLOWSIGNINRECOVERY = 'allowSignInRecovery';
					
	const _ALLOWSIGNOUT = 'allowSignOut';
					
	const _ALLOWUSERNAMEEDITING = 'allowUserNameEditing';
					
	const _ALTERNATEPHONENUMBERLABEL = 'alternatePhoneNumberLabel';
					
	const _BOTTOMFRAMEHEIGHT = 'bottomFrameHeight';
					
	const _BOTTOMFRAMEURL = 'bottomFrameURL';
					
	const _CUSTOMERSERVICEEMAILADDRESS = 'customerServiceEmailAddress';
					
	const _CUSTOMERSERVICEEMAILSIGNATURE = 'customerServiceEmailSignature';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENDUSERLICENSEAGREEMENT = 'endUserLicenseAgreement';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _HASENDUSERLICENSEAGREEMENT = 'hasEndUserLicenseAgreement';
					
	const _HEADERHEIGHT = 'headerHeight';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LEFTFRAMEURL = 'leftFrameURL';
					
	const _LEFTFRAMEWIDTH = 'leftFrameWidth';
					
	const _LOCKED = 'locked';
					
	const _MINIMUMPASSWORDLENGTH = 'minimumPasswordLength';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PHONENUMBERLABEL = 'phoneNumberLabel';
					
	const _PURCHASENOTIFICATIONPASSWORD = 'purchaseNotificationPassword';
					
	const _PURCHASENOTIFICATIONURL = 'purchaseNotificationURL';
					
	const _PURCHASENOTIFICATIONUSERNAME = 'purchaseNotificationUserName';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREADDRESS = 'requireAddress';
					
	const _REQUIREALTERNATEPHONENUMBER = 'requireAlternatePhoneNumber';
					
	const _REQUIRECITY = 'requireCity';
					
	const _REQUIRECOMPANY = 'requireCompany';
					
	const _REQUIRECOUNTRY = 'requireCountry';
					
	const _REQUIRECREDITCARD = 'requireCreditCard';
					
	const _REQUIREEMAILADDRESS = 'requireEmailAddress';
					
	const _REQUIREFIRSTNAME = 'requireFirstName';
					
	const _REQUIRELASTNAME = 'requireLastName';
					
	const _REQUIREPASSWORD = 'requirePassword';
					
	const _REQUIREPHONENUMBER = 'requirePhoneNumber';
					
	const _REQUIREPOSTALCODE = 'requirePostalCode';
					
	const _REQUIRESIGNIN = 'requireSignIn';
					
	const _REQUIRESIGNINCONFIRMATION = 'requireSignInConfirmation';
					
	const _REQUIRESTATE = 'requireState';
					
	const _RIGHTFRAMEURL = 'rightFrameURL';
					
	const _RIGHTFRAMEWIDTH = 'rightFrameWidth';
					
	const _SENDPAYMENTFAILUREEMAILS = 'sendPaymentFailureEmails';
					
	const _SENDRECEIPTS = 'sendReceipts';
					
	const _SENDSIGNINCONFIRMATION = 'sendSignInConfirmation';
					
	const _SHOPPINGCARTIMAGEURL = 'shoppingCartImageURL';
					
	const _SHOWAIRDATE = 'showAirdate';
					
	const _SHOWAUTHOR = 'showAuthor';
					
	const _SHOWPURCHASENOTIFICATIONURLRESPONSE = 'showPurchaseNotificationURLResponse';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTPAGECOUNT = 'storefrontPageCount';
					
	const _STOREFRONTPAGEIDS = 'storefrontPageIDs';
					
	const _STOREFRONTPAGETITLES = 'storefrontPageTitles';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TOPFRAMEHEIGHT = 'topFrameHeight';
					
	const _TOPFRAMEURL = 'topFrameURL';
					
	const _USEEMAILADDRESSASUSERNAME = 'useEmailAddressAsUserName';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastUsagePlanField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWBROWSING = 'allowBrowsing';
					
	const _ALLOWCUSTOMSERVERRELEASES = 'allowCustomServerReleases';
					
	const _ALLOWDOWNLOADS = 'allowDownloads';
					
	const _ALLOWPUSHING = 'allowPushing';
					
	const _ALLOWEDACCOUNTIDS = 'allowedAccountIDs';
					
	const _ALLOWEDACCOUNTNAMES = 'allowedAccountNames';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _DESCRIPTION = 'description';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MEDIAIDS = 'mediaIDs';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PLAYLISTIDS = 'playlistIDs';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEndUserTransactionType extends SoapObject
{				
	const _ADDED = 'Added';
					
	const _AUTOMATICALLY_RENEW = 'Automatically Renew';
					
	const _CUSTOM = 'Custom';
					
	const _DELETED = 'Deleted';
					
	const _DISABLED = 'Disabled';
					
	const _DO_NOT_RENEW = 'Do Not Renew';
					
	const _ENABLED = 'Enabled';
					
	const _EXPIRED = 'Expired';
					
	const _GOT_LICENSE = 'Got License';
					
	const _REFUND = 'Refund';
					
	const _RENEWED = 'Renewed';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEndUserTransactionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AMOUNTBILLABLE = 'amountBillable';
					
	const _AMOUNTDUE = 'amountDue';
					
	const _AMOUNTPAIDTOTAL = 'amountPaidTotal';
					
	const _AMOUNTPAIDWITHCARD = 'amountPaidWithCard';
					
	const _AMOUNTPAIDWITHOUTCARD = 'amountPaidWithoutCard';
					
	const _AUTOMATICALLYCOLLECTPAYMENT = 'automaticallyCollectPayment';
					
	const _COLLECTPAYMENT = 'collectPayment';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _COUPONCODE = 'couponCode';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _DESCRIPTION = 'description';
					
	const _ENDUSERCOUNTRY = 'endUserCountry';
					
	const _ENDUSERFIRSTNAME = 'endUserFirstName';
					
	const _ENDUSERID = 'endUserID';
					
	const _ENDUSERLASTNAME = 'endUserLastName';
					
	const _ENDUSERNAME = 'endUserName';
					
	const _ENDUSERPERMISSIONID = 'endUserPermissionID';
					
	const _ENDUSERPOSTALCODE = 'endUserPostalCode';
					
	const _ENDUSERSTATE = 'endUserState';
					
	const _ENDUSERTRANSACTIONTYPE = 'endUserTransactionType';
					
	const _EXTERNALIDS = 'externalIDs';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTPAYMENTCOLLECTED = 'lastPaymentCollected';
					
	const _LICENSEID = 'licenseID';
					
	const _LICENSETITLE = 'licenseTitle';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PAIDINFULL = 'paidInFull';
					
	const _POSTED = 'posted';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REFUNDPARTIALPAYMENT = 'refundPartialPayment';
					
	const _REFUNDPAYMENT = 'refundPayment';
					
	const _RELATEDTRANSACTIONID = 'relatedTransactionID';
					
	const _RELEASEID = 'releaseID';
					
	const _RENEWALNUMBER = 'renewalNumber';
					
	const _SALESTAX = 'salesTax';
					
	const _SALESTAXRATE = 'salesTaxRate';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTID = 'storefrontID';
					
	const _STOREFRONTTITLE = 'storefrontTitle';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastPriceField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _COUPONCODE = 'couponCode';
					
	const _DESCRIPTION = 'description';
					
	const _INITIALPRICE = 'initialPrice';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LICENSEID = 'licenseID';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PERIODSPERRENEWALCHARGE = 'periodsPerRenewalCharge';
					
	const _PREVENTDIRECTUSE = 'preventDirectUse';
					
	const _PRICEPERLICENSE = 'pricePerLicense';
					
	const _RENEWALCHARGESATSPECIALPRICE = 'renewalChargesAtSpecialPrice';
					
	const _RENEWALPRICE = 'renewalPrice';
					
	const _SPECIALRENEWALPRICE = 'specialRenewalPrice';
					
	const _VERSION = 'version';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastStorefrontOrderType extends SoapObject
{				
	const _CATEGORY = 'Category';
					
	const _LICENSE = 'License';
					
	const _MEDIA = 'Media';
					
	const _PLAYLIST = 'Playlist';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
}
	
class ComcastEndUserSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserField';
			case 'tieBreaker':
				return 'ComcastEndUserSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastEndUserField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastEndUserPermissionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserPermissionField';
			case 'tieBreaker':
				return 'ComcastEndUserPermissionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastEndUserPermissionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserPermissionSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastLicenseSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastLicenseField';
			case 'tieBreaker':
				return 'ComcastLicenseSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastLicenseField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastLicenseSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastPortalSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastPortalField';
			case 'tieBreaker':
				return 'ComcastPortalSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastPortalField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastPortalSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastStorefrontPageSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastStorefrontPageField';
			case 'tieBreaker':
				return 'ComcastStorefrontPageSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastStorefrontPageField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastStorefrontPageSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastStorefrontSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastStorefrontField';
			case 'tieBreaker':
				return 'ComcastStorefrontSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastStorefrontField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastStorefrontSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastUsagePlanSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastUsagePlanField';
			case 'tieBreaker':
				return 'ComcastUsagePlanSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastUsagePlanField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastUsagePlanSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastEndUserTransactionSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastEndUserTransactionField';
			case 'tieBreaker':
				return 'ComcastEndUserTransactionSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastEndUserTransactionField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastEndUserTransactionSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastPriceSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastPriceField';
			case 'tieBreaker':
				return 'ComcastPriceSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	/**
	 * @var ComcastPriceField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastPriceSort
	 **/
	public $tieBreaker;
				
}
	
class ComcastArrayOflong extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
}
	
class ComcastArrayOffloat extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("float");	
	}
					
}
	
