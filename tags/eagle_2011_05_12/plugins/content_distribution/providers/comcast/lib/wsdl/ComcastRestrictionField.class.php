<?php


class ComcastRestrictionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _AVAILABLETIME = 'availableTime';
					
	const _AVAILABLETIMEUNITS = 'availableTimeUnits';
					
	const _DELIVERY = 'delivery';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRATIONTIME = 'expirationTime';
					
	const _EXPIRATIONTIMEUNITS = 'expirationTimeUnits';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LIMITTOENDUSERNAMES = 'limitToEndUserNames';
					
	const _LIMITTOEXTERNALGROUPS = 'limitToExternalGroups';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REQUIREDRM = 'requireDRM';
					
	const _RETENTIONDATE = 'retentionDate';
					
	const _RETENTIONTIME = 'retentionTime';
					
	const _RETENTIONTIMEUNITS = 'retentionTimeUnits';
					
	const _TITLE = 'title';
					
	const _UNAPPROVEDATE = 'unapproveDate';
					
	const _UNAPPROVETIME = 'unapproveTime';
					
	const _UNAPPROVETIMEUNITS = 'unapproveTimeUnits';
					
	const _USEAIRDATE = 'useAirdate';
					
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


