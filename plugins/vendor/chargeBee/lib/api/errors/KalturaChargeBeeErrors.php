<?php
/**
 * @package plugins.chargeBee
 * @subpackage api.errors
 */
class KalturaChargeBeeErrors extends KalturaErrors
{
	const NO_VENDOR_CONFIGURATION  = 'NO_VENDOR_CONFIGURATION;;Vendor configuration file was not found!';
	const CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND = "CHARGE_BEE_VENDOR_INTEGRATION_NOT_FOUND;ID;ChargeBee vendor integration with id provided not found [@ID@]";
}