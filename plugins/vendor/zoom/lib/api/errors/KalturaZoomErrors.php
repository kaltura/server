<?php
/**
 * @package plugins.vendor
 * @subpackage api.errors
 */
class KalturaZoomErrors extends KalturaErrors
{
	const NO_INTEGRATION_DATA = 'NO_INTEGRATION_DATA;;Zoom integration data does not exist for current account';
	const NO_VENDOR_CONFIGURATION = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const ZOOM_ADMIN_REQUIRED = 'ZOOM_ADMIN_REQUIRED;;Only Zoom admins are allowed to access kaltura configuration page, please check your user account';
	const UNABLE_TO_FIND_SUBMIT_PAGE = 'UNABLE_TO_FIND_SUBMIT_PAGE;;unable to find submit page, please contact support';
}