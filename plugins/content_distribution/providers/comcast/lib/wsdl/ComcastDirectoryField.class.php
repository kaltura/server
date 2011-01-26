<?php


class ComcastDirectoryField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _GRANTACCESSIFUNAVAILABLE = 'grantAccessIfUnavailable';
					
	const _HOST = 'host';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PORT = 'port';
					
	const _PRIORITY = 'priority';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _SCOPE = 'scope';
					
	const _SEARCHPATTERN = 'searchPattern';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TITLE = 'title';
					
	const _USESSL = 'useSSL';
					
	const _USERNAME = 'userName';
					
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


