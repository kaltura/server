<?php


class ComcastAssetTypeField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLYBYDEFAULT = 'applyByDefault';
					
	const _APPLYTOTHUMBNAILSBYDEFAULT = 'applyToThumbnailsByDefault';
					
	const _DESCRIPTION = 'description';
					
	const _GUID = 'guid';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHAREWITHACCOUNTIDS = 'shareWithAccountIDs';
					
	const _SHAREWITHACCOUNTS = 'shareWithAccounts';
					
	const _SHAREWITHALLACCOUNTS = 'shareWithAllAccounts';
					
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


