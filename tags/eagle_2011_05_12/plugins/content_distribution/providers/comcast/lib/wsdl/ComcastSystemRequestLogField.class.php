<?php


class ComcastSystemRequestLogField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _CURRENTDATE = 'currentDate';
					
	const _DESCRIPTION = 'description';
					
	const _FAILEDAVERAGERESPONSETIME = 'failedAverageResponseTime';
					
	const _FAILEDAVERAGERESPONSETIMES = 'failedAverageResponseTimes';
					
	const _FAILEDREQUESTCOUNT = 'failedRequestCount';
					
	const _FAILEDREQUESTCOUNTS = 'failedRequestCounts';
					
	const _FAILEDREQUESTSPERHOUR = 'failedRequestsPerHour';
					
	const _FAILEDREQUESTSPERMINUTE = 'failedRequestsPerMinute';
					
	const _FAILEDREQUESTSPERSECOND = 'failedRequestsPerSecond';
					
	const _FAILURERATE = 'failureRate';
					
	const _FAILURERATES = 'failureRates';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REQUESTCOUNT = 'requestCount';
					
	const _REQUESTCOUNTS = 'requestCounts';
					
	const _REQUESTSPERHOUR = 'requestsPerHour';
					
	const _REQUESTSPERMINUTE = 'requestsPerMinute';
					
	const _REQUESTSPERSECOND = 'requestsPerSecond';
					
	const _SAMPLEENDDATE = 'sampleEndDate';
					
	const _SAMPLELENGTH = 'sampleLength';
					
	const _SAMPLESTARTDATE = 'sampleStartDate';
					
	const _SERVERADDRESS = 'serverAddress';
					
	const _SERVERNAME = 'serverName';
					
	const _SUCCESSFULAVERAGERESPONSETIME = 'successfulAverageResponseTime';
					
	const _SUCCESSFULAVERAGERESPONSETIMES = 'successfulAverageResponseTimes';
					
	const _SYSTEMREQUESTTYPE = 'systemRequestType';
					
	const _TOTALFAILEDREQUESTCOUNT = 'totalFailedRequestCount';
					
	const _TOTALREQUESTCOUNT = 'totalRequestCount';
					
	const _TOTALSUCCESSFULREQUESTCOUNT = 'totalSuccessfulRequestCount';
					
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


