<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


