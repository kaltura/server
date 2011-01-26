<?php


class ComcastAdminView extends SoapObject
{				
	const _ACCOUNTS = 'Accounts';
					
	const _ASSET_TYPES = 'Asset Types';
					
	const _CATEGORIES = 'Categories';
					
	const _CHOICES = 'Choices';
					
	const _CUSTOM_COMMANDS = 'Custom Commands';
					
	const _CUSTOM_FIELDS = 'Custom Fields';
					
	const _DEFAULTS = 'Defaults';
					
	const _DIRECTORIES = 'Directories';
					
	const _ENCODING = 'Encoding';
					
	const _END_USER_PERMISSIONS = 'End-User Permissions';
					
	const _END_USERS = 'End-Users';
					
	const _FILES = 'Files';
					
	const _GENERAL_SETTINGS = 'General Settings';
					
	const _LICENSES = 'Licenses';
					
	const _MEDIA = 'Media';
					
	const _PAGES = 'Pages';
					
	const _PERMISSIONS = 'Permissions';
					
	const _PERSONAL_INFO = 'Personal Info';
					
	const _PLAYLISTS = 'Playlists';
					
	const _PORTALS = 'Portals';
					
	const _RELEASES = 'Releases';
					
	const _REQUEST_LOGS = 'Request Logs';
					
	const _RESTRICTIONS = 'Restrictions';
					
	const _ROLES = 'Roles';
					
	const _SERVERS = 'Servers';
					
	const _STOREFRONTS = 'Storefronts';
					
	const _SYSTEM_STATUS = 'System Status';
					
	const _SYSTEM_TASKS = 'System Tasks';
					
	const _TRANSACTIONS = 'Transactions';
					
	const _USAGE_PLANS = 'Usage Plans';
					
	const _USAGE_REPORTS = 'Usage Reports';
					
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


