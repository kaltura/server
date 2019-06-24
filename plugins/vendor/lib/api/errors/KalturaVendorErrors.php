<?php
/**
 * @package plugins.vendor
 * @subpackage api.errors
 */
class KalturaVendorErrors extends KalturaErrors
{
	const NO_INTEGRATION_DATA = 'NO_INTEGRATION_DATA;;Zoom integration data does not exist for current account';
	const NO_VENDOR_CONFIGURATION = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const ZOOM_ADMIN_REQUIRED = 'ZOOM_ADMIN_REQUIRED;;Only Zoom admins are allowed to access kaltura configuration page, please check your user account';
}