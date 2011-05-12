<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


