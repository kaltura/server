<?php


class ComcastSystemTaskField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountId';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _DESCRIPTION = 'description';
					
	const _DESTINATION = 'destination';
					
	const _DESTINATIONLOCATION = 'destinationLocation';
					
	const _DIAGNOSTICS = 'diagnostics';
					
	const _FAILEDATTEMPTS = 'failedAttempts';
					
	const _ITEM = 'item';
					
	const _JOB = 'job';
					
	const _JOBID = 'jobID';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PERCENTCOMPLETE = 'percentComplete';
					
	const _REFRESH = 'refresh';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREDSERVICETOKEN = 'requiredServiceToken';
					
	const _SERVICETOKEN = 'serviceToken';
					
	const _SOURCE = 'source';
					
	const _SOURCELOCATION = 'sourceLocation';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TASKTYPE = 'taskType';
					
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


