<?php


class ComcastLicenseField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _APPLIESTO = 'appliesTo';
					
	const _AUTHENTICATION = 'authentication';
					
	const _AUTHENTICATIONURL = 'authenticationURL';
					
	const _AUTOMATICALLYRENEWBYDEFAULT = 'automaticallyRenewByDefault';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _CATEGORYIDS = 'categoryIDs';
					
	const _DEFAULTINITIALPRICE = 'defaultInitialPrice';
					
	const _DESCRIPTION = 'description';
					
	const _DIRECTORIES = 'directories';
					
	const _DIRECTORYIDS = 'directoryIDs';
					
	const _DISABLEBACKUPS = 'disableBackups';
					
	const _DISABLEONCLOCKROLLBACK = 'disableOnClockRollback';
					
	const _DISABLEONPC = 'disableOnPC';
					
	const _DISABLED = 'disabled';
					
	const _DRMKEYID = 'drmKeyID';
					
	const _ENDUSERIDS = 'endUserIDs';
					
	const _ENDUSERNAMES = 'endUserNames';
					
	const _ENDUSERPERMISSIONCOUNT = 'endUserPermissionCount';
					
	const _ENDUSERPERMISSIONIDS = 'endUserPermissionIDs';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXPIRATIONTIME = 'expirationTime';
					
	const _EXPIRATIONTIMEAFTERFIRSTUSE = 'expirationTimeAfterFirstUse';
					
	const _EXPIRATIONTIMEAFTERFIRSTUSEUNITS = 'expirationTimeAfterFirstUseUnits';
					
	const _EXPIRATIONTIMEUNITS = 'expirationTimeUnits';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _FORMATS = 'formats';
					
	const _HIGHESTBITRATE = 'highestBitrate';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LICENSESPERENDUSER = 'licensesPerEndUser';
					
	const _LOCKED = 'locked';
					
	const _LOWESTBITRATE = 'lowestBitrate';
					
	const _MAXIMUMBURNS = 'maximumBurns';
					
	const _MAXIMUMPLAYS = 'maximumPlays';
					
	const _MAXIMUMRENEWALS = 'maximumRenewals';
					
	const _MAXIMUMTRANSFERSTODEVICE = 'maximumTransfersToDevice';
					
	const _MEDIAIDS = 'mediaIDs';
					
	const _MINIMUMRENEWALS = 'minimumRenewals';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PARENTLICENSE = 'parentLicense';
					
	const _PARENTLICENSEID = 'parentLicenseID';
					
	const _PLAYLISTIDS = 'playlistIDs';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREINDIVIDUALIZATION = 'requireIndividualization';
					
	const _REQUIRESECUREPLAYER = 'requireSecurePlayer';
					
	const _SHOWINPICKER = 'showInPicker';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _SUBSCRIPTIONGRACEPERIOD = 'subscriptionGracePeriod';
					
	const _SUBSCRIPTIONGRACEPERIODUNITS = 'subscriptionGracePeriodUnits';
					
	const _SUBSCRIPTIONTRIALPERIOD = 'subscriptionTrialPeriod';
					
	const _SUBSCRIPTIONTRIALPERIODUNITS = 'subscriptionTrialPeriodUnits';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
	const _TEMPLATELICENSETITLE = 'templateLicenseTitle';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TIMEALLOWED = 'timeAllowed';
					
	const _TIMEALLOWEDUNITS = 'timeAllowedUnits';
					
	const _TITLE = 'title';
					
	const _USEDRM = 'useDRM';
					
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


