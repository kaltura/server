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
	 * @param string $response
	 * @return array<tokens>
	 */
	function extractTokensFromResponse($response);

	/**
	 * @param string $oldRefreshToken
	 * @return string newAccessToken
	 */
	function refreshTokens($oldRefreshToken);


}