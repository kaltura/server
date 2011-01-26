<?php


class ComcastEndUserPermissionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUTOMATICALLYRENEW = 'automaticallyRenew';
					
	const _AVAILABLEDATE = 'availableDate';
					
	const _COUPONCODE = 'couponCode';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _CURRENTPLAYS = 'currentPlays';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENDUSERID = 'endUserID';
					
	const _ENDUSERNAME = 'endUserName';
					
	const _EXPIRATIONDATE = 'expirationDate';
					
	const _EXTERNALID = 'externalID';
					
	const _GRANTDATE = 'grantDate';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTRENEWED = 'lastRenewed';
					
	const _LICENSEID = 'licenseID';
					
	const _LICENSEMEDIAIDS = 'licenseMediaIDs';
					
	const _LICENSEPLAYLISTIDS = 'licensePlaylistIDs';
					
	const _LICENSETITLE = 'licenseTitle';
					
	const _LICENSESGRANTED = 'licensesGranted';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PREPAID = 'prepaid';
					
	const _PRICEID = 'priceID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REMAININGLICENSES = 'remainingLicenses';
					
	const _REMAININGPLAYS = 'remainingPlays';
					
	const _RENEW = 'renew';
					
	const _RENEWABLE = 'renewable';
					
	const _RENEWALS = 'renewals';
					
	const _RETRYLASTPAYMENT = 'retryLastPayment';
					
	const _SALESTAXRATE = 'salesTaxRate';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTID = 'storefrontID';
					
	const _STOREFRONTTITLE = 'storefrontTitle';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
	const _TOTALPLAYS = 'totalPlays';
					
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


