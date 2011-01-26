<?php


class ComcastRoleField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWAPICALLS = 'allowAPICalls';
					
	const _ALLOWCONSOLEACCESS = 'allowConsoleAccess';
					
	const _CAPABILITIES = 'capabilities';
					
	const _COPYTONEWACCOUNTS = 'copyToNewAccounts';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _GRANTBYDEFAULT = 'grantByDefault';
					
	const _GRANTFUTURECAPABILITIES = 'grantFutureCapabilities';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHOWHOMETAB = 'showHomeTab';
					
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


