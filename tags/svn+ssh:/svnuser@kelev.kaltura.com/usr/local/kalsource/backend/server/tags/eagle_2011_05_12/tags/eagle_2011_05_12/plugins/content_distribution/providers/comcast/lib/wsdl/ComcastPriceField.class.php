<?php


class ComcastPriceField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _COUPONCODE = 'couponCode';
					
	const _DESCRIPTION = 'description';
					
	const _INITIALPRICE = 'initialPrice';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LICENSEID = 'licenseID';
					
	const _LOCKED = 'locked';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PERIODSPERRENEWALCHARGE = 'periodsPerRenewalCharge';
					
	const _PREVENTDIRECTUSE = 'preventDirectUse';
					
	const _PRICEPERLICENSE = 'pricePerLicense';
					
	const _RENEWALCHARGESATSPECIALPRICE = 'renewalChargesAtSpecialPrice';
					
	const _RENEWALPRICE = 'renewalPrice';
					
	const _SPECIALRENEWALPRICE = 'specialRenewalPrice';
					
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


