<?php


class ComcastPermissionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACCOUNTID = 'accountID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLYTOSUBACCOUNTS = 'applyToSubAccounts';
					
	const _DESCRIPTION = 'description';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _ROLEIDS = 'roleIDs';
					
	const _ROLETITLES = 'roleTitles';
					
	const _SHOWHOMETAB = 'showHomeTab';
					
	const _USERADDED = 'userAdded';
					
	const _USEREMAILADDRESS = 'userEmailAddress';
					
	const _USERID = 'userID';
					
	const _USERNAME = 'userName';
					
	const _USEROWNER = 'userOwner';
					
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


