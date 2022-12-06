<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.errors
 */
class KalturaWebexAPIErrors extends KalturaVendorIntegrationErrors
{
	const NO_INTEGRATION_DATA        = 'NO_INTEGRATION_DATA;;Webex integration data does not exist for current account';
	const NO_WEBEX_ACCOUNT_CONFIGURATION    = 'NO_VENDOR_CONFIGURATION;;Webex Account configuration was not found';
	const MISSING_CONFIGURATION_PARAMETER    = 'MISSING_CONFIGURATION_PARAMETER;PARAMETER;Configuration parameter \"@PARAMETER@\" not found in Webex section of vendor map';
	const WEBEX_ADMIN_REQUIRED        = 'WEBEX_ADMIN_REQUIRED;;Only Webex admins are allowed to access kaltura configuration page, please check your user account';
	const UNABLE_TO_AUTHENTICATE     = 'UNABLE_TO_AUTHENTICATE;;Unable to authenticate because both Refresh Token and Access Token are missing';
	const EXCEEDED_MAX_WEBEX_API_DROP_FOLDERS = 'EXCEEDED_MAX_WEBEX_API_DROP_FOLDERS;;Amount of maximum Webex API drop folders per partner exceeded';
	const REGIONAL_REDIRECT_PAGE_NOT_FOUND = "REGIONAL_REDIRECT_PAGE_NOT_FOUND;;Unable to find regional redirect page for Webex integration";
	const RETRIEVE_USER_FAILED = "RETRIEVE_USER_FAILED;;Retrieve user from vendor failed";
}
