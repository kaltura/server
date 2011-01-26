<?php


class ComcastStorefrontField extends SoapObject
{				
	const _ID = 'ID';
					
	const _PID = 'PID';
					
	const _URL = 'URL';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AIRDATEFORMAT = 'airdateFormat';
					
	const _ALLOWSELFEDITING = 'allowSelfEditing';
					
	const _ALLOWSELFREGISTRATION = 'allowSelfRegistration';
					
	const _ALLOWSIGNINRECOVERY = 'allowSignInRecovery';
					
	const _ALLOWSIGNOUT = 'allowSignOut';
					
	const _ALLOWUSERNAMEEDITING = 'allowUserNameEditing';
					
	const _ALTERNATEPHONENUMBERLABEL = 'alternatePhoneNumberLabel';
					
	const _BOTTOMFRAMEHEIGHT = 'bottomFrameHeight';
					
	const _BOTTOMFRAMEURL = 'bottomFrameURL';
					
	const _CUSTOMERSERVICEEMAILADDRESS = 'customerServiceEmailAddress';
					
	const _CUSTOMERSERVICEEMAILSIGNATURE = 'customerServiceEmailSignature';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENDUSERLICENSEAGREEMENT = 'endUserLicenseAgreement';
					
	const _EXTERNALGROUPS = 'externalGroups';
					
	const _HASENDUSERLICENSEAGREEMENT = 'hasEndUserLicenseAgreement';
					
	const _HEADERHEIGHT = 'headerHeight';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LEFTFRAMEURL = 'leftFrameURL';
					
	const _LEFTFRAMEWIDTH = 'leftFrameWidth';
					
	const _LOCKED = 'locked';
					
	const _MINIMUMPASSWORDLENGTH = 'minimumPasswordLength';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PHONENUMBERLABEL = 'phoneNumberLabel';
					
	const _PURCHASENOTIFICATIONPASSWORD = 'purchaseNotificationPassword';
					
	const _PURCHASENOTIFICATIONURL = 'purchaseNotificationURL';
					
	const _PURCHASENOTIFICATIONUSERNAME = 'purchaseNotificationUserName';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REQUIREADDRESS = 'requireAddress';
					
	const _REQUIREALTERNATEPHONENUMBER = 'requireAlternatePhoneNumber';
					
	const _REQUIRECITY = 'requireCity';
					
	const _REQUIRECOMPANY = 'requireCompany';
					
	const _REQUIRECOUNTRY = 'requireCountry';
					
	const _REQUIRECREDITCARD = 'requireCreditCard';
					
	const _REQUIREEMAILADDRESS = 'requireEmailAddress';
					
	const _REQUIREFIRSTNAME = 'requireFirstName';
					
	const _REQUIRELASTNAME = 'requireLastName';
					
	const _REQUIREPASSWORD = 'requirePassword';
					
	const _REQUIREPHONENUMBER = 'requirePhoneNumber';
					
	const _REQUIREPOSTALCODE = 'requirePostalCode';
					
	const _REQUIRESIGNIN = 'requireSignIn';
					
	const _REQUIRESIGNINCONFIRMATION = 'requireSignInConfirmation';
					
	const _REQUIRESTATE = 'requireState';
					
	const _RIGHTFRAMEURL = 'rightFrameURL';
					
	const _RIGHTFRAMEWIDTH = 'rightFrameWidth';
					
	const _SENDPAYMENTFAILUREEMAILS = 'sendPaymentFailureEmails';
					
	const _SENDRECEIPTS = 'sendReceipts';
					
	const _SENDSIGNINCONFIRMATION = 'sendSignInConfirmation';
					
	const _SHOPPINGCARTIMAGEURL = 'shoppingCartImageURL';
					
	const _SHOWAIRDATE = 'showAirdate';
					
	const _SHOWAUTHOR = 'showAuthor';
					
	const _SHOWPURCHASENOTIFICATIONURLRESPONSE = 'showPurchaseNotificationURLResponse';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTPAGECOUNT = 'storefrontPageCount';
					
	const _STOREFRONTPAGEIDS = 'storefrontPageIDs';
					
	const _STOREFRONTPAGETITLES = 'storefrontPageTitles';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _THUMBNAILURL = 'thumbnailURL';
					
	const _TITLE = 'title';
					
	const _TOPFRAMEHEIGHT = 'topFrameHeight';
					
	const _TOPFRAMEURL = 'topFrameURL';
					
	const _USEEMAILADDRESSASUSERNAME = 'useEmailAddressAsUserName';
					
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


