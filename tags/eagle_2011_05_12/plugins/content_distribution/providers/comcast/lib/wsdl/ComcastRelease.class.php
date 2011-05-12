<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
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


