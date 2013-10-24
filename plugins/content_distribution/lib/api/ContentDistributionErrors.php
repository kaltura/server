<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api
 */
class ContentDistributionErrors extends KalturaErrors
{
	const DISTRIBUTION_PROVIDER_NOT_FOUND = "DISTRIBUTION_PROVIDER_NOT_FOUND;TYPE;Distrbution provider type not found [@TYPE@]";
	
	const DISTRIBUTION_PROFILE_NOT_FOUND = "DISTRIBUTION_PROFILE_NOT_FOUND;PROFILE_ID;Distrbution profile not found [@PROFILE_ID@]";
	
	const DISTRIBUTION_PROFILE_DISABLED = "DISTRIBUTION_PROFILE_DISABLED;PROFILE_ID;Distrbution profile disabled [@PROFILE_ID@]";
	
	const ENTRY_DISTRIBUTION_ALREADY_EXISTS = "ENTRY_DISTRIBUTION_ALREADY_EXISTS;ENTRY_ID,PROFILE_ID;Entry distrbution already exists for entry id [@ENTRY_ID@] and profile id [@PROFILE_ID@]";
	
	const ENTRY_DISTRIBUTION_NOT_FOUND = "ENTRY_DISTRIBUTION_NOT_FOUND;ENTRY_ID;Entry distrbution not found [@ENTRY_ID@]";
	
	const ENTRY_DISTRIBUTION_MISSING_LOG = "ENTRY_DISTRIBUTION_MISSING_LOG;ENTRY_ID,PROFILE_ID;Entry distrbution [@ENTRY_ID@] file type [@PROFILE_ID@]";
	
	const ENTRY_DISTRIBUTION_STATUS = "ENTRY_DISTRIBUTION_STATUS;ENTRY_ID,STATUS;Entry distrbution [@ENTRY_ID@] wrong status [@STATUS@]";
	
	const GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND = "GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND;PROVIDER_ID;Generic distrbution provider not found [@PROVIDER_ID@]";
	
	const GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND = "GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND;PROVIDER_ID;Generic distrbution provider action not found [@PROVIDER_ID@]";
	
	const CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER = "CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER;;Cannot delete default generic distribution provider";
	
	const INVALID_FEED_URL = "INVALID_FEED_URL;;Invalid feed URL";
}