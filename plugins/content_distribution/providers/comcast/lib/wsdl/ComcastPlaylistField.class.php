<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


