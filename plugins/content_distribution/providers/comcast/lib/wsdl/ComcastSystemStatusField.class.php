<?php


class ComcastSystemStatusField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _BUILDDATE = 'buildDate';
					
	const _CURRENTDATE = 'currentDate';
					
	const _DESCRIPTION = 'description';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _QUEUEDCONNECTIONS = 'queuedConnections';
					
	const _ROOTACCOUNT = 'rootAccount';
					
	const _ROOTACCOUNTID = 'rootAccountID';
					
	const _SERVERADDRESS = 'serverAddress';
					
	const _SERVERNAME = 'serverName';
					
	const _SOFTWAREVERSION = 'softwareVersion';
					
	const _STARTDATE = 'startDate';
					
	const _UPTIME = 'upTime';
					
	const _UPTIMEWITHUNITS = 'upTimeWithUnits';
					
	const _USAGETRACKINGLOAD = 'usageTrackingLoad';
					
	const _VERSION = 'version';
					
	const _WEBXML = 'webXML';
					
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


