<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoomOauth
 */
interface kVendorOauth
{

	/**
	 * @return mixed
	 */
	function retrieveTokensData();

	/**
	 * @param string $oldRefreshToken
	 * @return string newAccessToken
	 */
	function refreshTokens($oldRefreshToken);


}