<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kZoomTokens
{
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const EXPIRES_IN = 'expires_in';

	protected $zoomBaseURL;
	protected $accountId;
	protected $zoomAuthType;

	public function __construct($zoomBaseURL, $accountId, $zoomAuthType)
	{
		$this->zoomBaseURL = $zoomBaseURL;
		$this->accountId = $accountId;
		$this->zoomAuthType = $zoomAuthType;
	}

	public static function isTokenExpired($expiresIn)
	{
		if ($expiresIn <= time() +
			kconf::getArrayValue('tokenExpiryGrace', ZoomHelper::ZOOM_ACCOUNT_PARAM, ZoomHelper::VENDOR_MAP, 600))
		{
			return true;
		}
		return false;
	}
}