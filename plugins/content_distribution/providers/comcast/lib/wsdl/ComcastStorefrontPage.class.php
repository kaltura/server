<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
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


