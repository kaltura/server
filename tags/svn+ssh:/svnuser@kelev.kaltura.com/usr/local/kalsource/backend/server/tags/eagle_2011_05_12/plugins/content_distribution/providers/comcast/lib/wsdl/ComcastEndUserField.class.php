<?php


class ComcastEndUserField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACCEPTEDLICENSEAGREEMENT = 'acceptedLicenseAgreement';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ADDRESS = 'address';
					
	const _ALTERNATEPHONENUMBER = 'alternatePhoneNumber';
					
	const _AUTHENTICATIONMETHOD = 'authenticationMethod';
					
	const _CITY = 'city';
					
	const _COMPANY = 'company';
					
	const _COUNTRY = 'country';
					
	const _CREDITCARDEXPIRATIONMONTH = 'creditCardExpirationMonth';
					
	const _CREDITCARDEXPIRATIONYEAR = 'creditCardExpirationYear';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDNUMBER = 'creditCardNumber';
					
	const _CREDITCARDSTATUS = 'creditCardStatus';
					
	const _CREDITCARDTOKEN = 'creditCardToken';
					
	const _CREDITCARDTOKENGENERATED = 'creditCardTokenGenerated';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _DISPLAYNAME = 'displayName';
					
	const _EMAILADDRESS = 'emailAddress';
					
	const _ENDUSERPERMISSIONCOUNT = 'endUserPermissionCount';
					
	const _ENDUSERPERMISSIONIDS = 'endUserPermissionIDs';
					
	const _FIRSTNAME = 'firstName';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTNAME = 'lastName';
					
	const _LICENSEIDS = 'licenseIDs';
					
	const _LOCKED = 'locked';
					
	const _NAMEONCREDITCARD = 'nameOnCreditCard';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PASSWORD = 'password';
					
	const _PHONENUMBER = 'phoneNumber';
					
	const _POSTALCODE = 'postalCode';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _STATE = 'state';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
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


