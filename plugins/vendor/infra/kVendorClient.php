<?php
/**
 * @package plugins.vendor
 * @subpackage model
 */
abstract class kVendorClient
{
	protected $baseURL;
	protected $refreshToken;
	protected $accessToken;
	protected $clientId;
	protected $clientSecret;
	protected $accessExpiresIn;
	protected $httpCode;
}
