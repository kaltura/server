<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoomOauth
 */
interface kVendorOauth
{
	/**
	 * @param bool $forceNewToken
	 * @return mixed
	 */
	function retrieveTokensData($forceNewToken = false);

	/**
	 * @param VendorIntegration $vendorIntegration
	 * @return string newAccessToken
	 */
	function refreshTokens($vendorIntegration);


}