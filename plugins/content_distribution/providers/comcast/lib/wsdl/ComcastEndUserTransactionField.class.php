<?php


class ComcastEndUserTransactionField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AMOUNTBILLABLE = 'amountBillable';
					
	const _AMOUNTDUE = 'amountDue';
					
	const _AMOUNTPAIDTOTAL = 'amountPaidTotal';
					
	const _AMOUNTPAIDWITHCARD = 'amountPaidWithCard';
					
	const _AMOUNTPAIDWITHOUTCARD = 'amountPaidWithoutCard';
					
	const _AUTOMATICALLYCOLLECTPAYMENT = 'automaticallyCollectPayment';
					
	const _COLLECTPAYMENT = 'collectPayment';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTTITLE = 'contentTitle';
					
	const _COUPONCODE = 'couponCode';
					
	const _CREDITCARDINFO = 'creditCardInfo';
					
	const _CREDITCARDTYPE = 'creditCardType';
					
	const _DESCRIPTION = 'description';
					
	const _ENDUSERCOUNTRY = 'endUserCountry';
					
	const _ENDUSERFIRSTNAME = 'endUserFirstName';
					
	const _ENDUSERID = 'endUserID';
					
	const _ENDUSERLASTNAME = 'endUserLastName';
					
	const _ENDUSERNAME = 'endUserName';
					
	const _ENDUSERPERMISSIONID = 'endUserPermissionID';
					
	const _ENDUSERPOSTALCODE = 'endUserPostalCode';
					
	const _ENDUSERSTATE = 'endUserState';
					
	const _ENDUSERTRANSACTIONTYPE = 'endUserTransactionType';
					
	const _EXTERNALIDS = 'externalIDs';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LASTPAYMENTCOLLECTED = 'lastPaymentCollected';
					
	const _LICENSEID = 'licenseID';
					
	const _LICENSETITLE = 'licenseTitle';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PAIDINFULL = 'paidInFull';
					
	const _POSTED = 'posted';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _REFUNDPARTIALPAYMENT = 'refundPartialPayment';
					
	const _REFUNDPAYMENT = 'refundPayment';
					
	const _RELATEDTRANSACTIONID = 'relatedTransactionID';
					
	const _RELEASEID = 'releaseID';
					
	const _RENEWALNUMBER = 'renewalNumber';
					
	const _SALESTAX = 'salesTax';
					
	const _SALESTAXRATE = 'salesTaxRate';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _STOREFRONTID = 'storefrontID';
					
	const _STOREFRONTTITLE = 'storefrontTitle';
					
	const _TEMPLATELICENSEID = 'templateLicenseID';
					
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


