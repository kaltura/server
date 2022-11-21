<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.errors
 */
class KalturaWebexAPIErrors extends KalturaErrors
{
	const NO_INTEGRATION_DATA        = 'NO_INTEGRATION_DATA;;Webex integration data does not exist for current account';
	const TOKEN_PARSING_FAILED       = 'TOKEN_PARSING_FAILED;;Parsing tokens failed';
	const NO_VENDOR_CONFIGURATION    = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const MISSING_CONFIGURATION_PARAMETER    = 'MISSING_CONFIGURATION_PARAMETER;PARAMETER;Configuration parameter \"@PARAMETER@\" not found in Webex section of vendor map';
	const WEBEX_ADMIN_REQUIRED        = 'WEBEX_ADMIN_REQUIRED;;Only Webex admins are allowed to access kaltura configuration page, please check your user account';
	const UNABLE_TO_FIND_SUBMIT_PAGE = 'UNABLE_TO_FIND_SUBMIT_PAGE;;unable to find submit page, please contact support';
	const UNABLE_TO_AUTHENTICATE     = 'UNABLE_TO_AUTHENTICATE;;Unable to authenticate because both Refresh Token and JWT are missing';
	const NOT_ALLOWED_ON_THIS_INSTANCE        = 'NOT_ALLOWED_ON_THIS_INSTANCE;;Not allowed on this instance';
	const INTEGRATION_ALREADY_EXIST   = "INTEGRATION_ALREADY_EXIST;PARTNER_ID;Integration already exist for partner id \"@PARTNER_ID@\", please disable it and try again";
	const DROP_FOLDER_INTEGRATION_DATA_MISSING = 'DROP_FOLDER_INTEGRATION_DATA_MISSING;;Missing integration data for this drop folder';
	const EXCEEDED_MAX_WEBEX_API_DROP_FOLDERS = 'EXCEEDED_MAX_ZOOM_DROP_FOLDERS;;Amount of maximum Webex API drop folders per partner exceeded';
}
