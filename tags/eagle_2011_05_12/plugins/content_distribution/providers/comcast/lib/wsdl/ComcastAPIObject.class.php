<?php


class ComcastAPIObject extends SoapObject
{				
	const _ACCOUNT = 'Account';
					
	const _ASSETTYPE = 'AssetType';
					
	const _CATEGORY = 'Category';
					
	const _CHOICE = 'Choice';
					
	const _CUSTOMCOMMAND = 'CustomCommand';
					
	const _CUSTOMFIELD = 'CustomField';
					
	const _DIRECTORY = 'Directory';
					
	const _ENCODINGPROFILE = 'EncodingProfile';
					
	const _ENDUSER = 'EndUser';
					
	const _ENDUSERPERMISSION = 'EndUserPermission';
					
	const _ENDUSERTRANSACTION = 'EndUserTransaction';
					
	const _JOB = 'Job';
					
	const _LICENSE = 'License';
					
	const _LOCATION = 'Location';
					
	const _MEDIA = 'Media';
					
	const _MEDIAFILE = 'MediaFile';
					
	const _PERMISSION = 'Permission';
					
	const _PLAYLIST = 'Playlist';
					
	const _PORTAL = 'Portal';
					
	const _PRICE = 'Price';
					
	const _RELEASE = 'Release';
					
	const _REQUEST = 'Request';
					
	const _RESTRICTION = 'Restriction';
					
	const _ROLE = 'Role';
					
	const _SERVER = 'Server';
					
	const _STOREFRONT = 'Storefront';
					
	const _STOREFRONTPAGE = 'StorefrontPage';
					
	const _SYSTEMREQUESTLOG = 'SystemRequestLog';
					
	const _SYSTEMSTATUS = 'SystemStatus';
					
	const _SYSTEMTASK = 'SystemTask';
					
	const _USAGEPLAN = 'UsagePlan';
					
	const _USER = 'User';
					
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


