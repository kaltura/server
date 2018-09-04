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
	 * @param string $oldRefreshToken
	 * @return string newAccessToken
	 */
	function refreshTokens($oldRefreshToken);


}