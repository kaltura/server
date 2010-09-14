<?php
class AccessControlTestsHelpers
{
	static function getDummyRestrictions()
	{
		$countryRestriction = new KalturaCountryRestriction();
		$countryRestriction->countryRestrictionType = KalturaCountryRestrictionType::RESTRICT_COUNTRY_LIST;
		$countryRestriction->countryList = 'IL,US';
		
		$siteRestriction = new KalturaSiteRestriction();
		$siteRestriction->siteRestrictionType = KalturaSiteRestrictionType::ALLOW_SITE_LIST;
		$siteRestriction->siteList = 'google.com,msn.com';
		
		$restrictionArray = new KalturaRestrictionArray();
		$restrictionArray[] =$countryRestriction;
		$restrictionArray[] =$siteRestriction;
		
		return $restrictionArray;
	}
}