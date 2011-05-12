<?php


abstract class ComcastContent extends ComcastStatusObject
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
					
	public function __toString()
	{
		return print_r($this, true);	
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


