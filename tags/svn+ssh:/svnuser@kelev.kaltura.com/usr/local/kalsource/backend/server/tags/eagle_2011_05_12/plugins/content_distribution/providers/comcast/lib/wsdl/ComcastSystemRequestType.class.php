<?php


class ComcastSystemRequestType extends SoapObject
{				
	const _API = 'API';
					
	const _DATASERVICE = 'DataService';
					
	const _DYNAMICCHOICE = 'DynamicChoice';
					
	const _FMSSERVICE = 'FMSService';
					
	const _GEOTARGETEDCHOICE = 'GeoTargetedChoice';
					
	const _LDAP = 'LDAP';
					
	const _LICENSESERVER = 'LicenseServer';
					
	const _METAFILEFETCH = 'MetafileFetch';
					
	const _NOTIFICATION = 'Notification';
					
	const _PORTALCONTENTLIST = 'PortalContentList';
					
	const _PORTALPLAYER = 'PortalPlayer';
					
	const _RELEASE = 'Release';
					
	const _RSS = 'RSS';
					
	const _USAGEREPORT = 'UsageReport';
					
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


