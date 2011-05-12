<?php


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
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


