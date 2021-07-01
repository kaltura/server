<?php
/**
 * @package plugins.chargeBee
 * @subpackage api.errors
 */
class KalturaChargeBeeErrors extends KalturaErrors
{
	const NO_VENDOR_CONFIGURATION  = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND = "CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND;ID;ChargeBee vendor integration with id provided not found [@ID@]";
	const UNAUTHORIZED_USER_PASSWORD = 'UNAUTHORIZED_USER_PASSWORD;;Unauthorized user or password';
	const MISSING_USER_PASSWORD_CONFIGURATION = 'MISSING_USER_PASSWORD_CONFIGURATION;;Missing configured user or password';
	const MISSING_EVENT_TYPE = 'MISSING_EVENT_TYPE;;Missing event type in the request';
}