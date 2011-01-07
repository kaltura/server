<?php
class ContentDistributionErrors extends KalturaErrors
{
	const DISTRIBUTION_PROVIDER_NOT_FOUND = "DISTRIBUTION_PROVIDER_NOT_FOUND,Distrbution provider type not found [%s]";
	
	const DISTRIBUTION_PROFILE_NOT_FOUND = "DISTRIBUTION_PROFILE_NOT_FOUND,Distrbution profile not found [%s]";
	
	const DISTRIBUTION_PROFILE_DISABLED = "DISTRIBUTION_PROFILE_DISABLED,Distrbution profile disabled [%s]";
	
	const ENTRY_DISTRIBUTION_ALREADY_EXISTS = "ENTRY_DISTRIBUTION_ALREADY_EXISTS,Entry distrbution already exists for entry id [%s] and profile id [%s]";
	
	const ENTRY_DISTRIBUTION_NOT_FOUND = "ENTRY_DISTRIBUTION_NOT_FOUND,Entry distrbution not found [%s]";
	
	const ENTRY_DISTRIBUTION_STATUS = "ENTRY_DISTRIBUTION_STATUS,Entry distrbution [%s] wrong status [%s]";
	
	const GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND = "GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND,Generic distrbution provider not found [%s]";
	
	const GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND = "GENERIC_DISTRIBUTION_PROVIDER_ACTION_NOT_FOUND,Generic distrbution provider action not found [%s]";
	
	const CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER = "CANNOT_DELETE_DEFAULT_DISTRIBUTION_PROVIDER,Cannot delete default generic distribution provider";
}