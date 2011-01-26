<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


