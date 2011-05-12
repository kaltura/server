<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
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


