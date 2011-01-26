<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


