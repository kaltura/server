<?php


class ComcastUserField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUTHENTICATIONMETHOD = 'authenticationMethod';
					
	const _DESCRIPTION = 'description';
					
	const _EMAILADDRESS = 'emailAddress';
					
	const _FAILEDSIGNINATTEMPTS = 'failedSignInAttempts';
					
	const _LASTACCOUNTID = 'lastAccountID';
					
	const _LASTFAILEDSIGNINATTEMPT = 'lastFailedSignInAttempt';
					
	const _LASTFAILEDSIGNINATTEMPTIPADDRESS = 'lastFailedSignInAttemptIPAddress';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _NAME = 'name';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PERMISSIONIDS = 'permissionIDs';
					
	const _POSSIBLEPASSWORDATTACKDETECTED = 'possiblePasswordAttackDetected';
					
	const _PREVENTPASSWORDATTACKS = 'preventPasswordAttacks';
					
	const _TIMEZONE = 'timeZone';
					
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


