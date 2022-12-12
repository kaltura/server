<?php

/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
class KalturaVendorIntegrationErrors extends KalturaErrors
{
	const DROP_FOLDER_INVALID_INTEGRATION_TYPE = 'DROP_FOLDER_INVALID_INTEGRATION_TYPE;;Wrong vendor integration type for this drop folder';
	const TOKEN_PARSING_FAILED       = 'TOKEN_PARSING_FAILED;;Parsing tokens failed';
	const NO_VENDOR_CONFIGURATION    = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const INTEGRATION_ALREADY_EXIST   = "INTEGRATION_ALREADY_EXIST;PARTNER_ID;Integration already exist for partner id \"@PARTNER_ID@\", please disable it and try again";
	const DROP_FOLDER_INTEGRATION_DATA_MISSING = 'DROP_FOLDER_INTEGRATION_DATA_MISSING;;Missing integration data for this drop folder';
	const INCORRECT_OAUTH_STATE = "INCORRECT_OAUTH_STATE;;OAuth state does not match configuration";
	const SUBMIT_PAGE_NOT_FOUND = "SUBMIT_PAGE_NOT_FOUND;;Unable to find submit page for vendor integration";
	const RETRIEVE_USER_FAILED = "RETRIEVE_USER_FAILED;;Retrieve user from vendor failed";
	const DELETE_RECORDING_FAILED = "DELETE_RECORDING_FAILED;;Delete recording from vendor failed";
}
