<?php

/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage zoom.model
 */
class kWebexAPIClient
{
	/**
	 * kWebexAPIClient constructor.
	 * @param $webexBaseURL
	 * @param null $refreshToken
	 * @param null $clientId
	 * @param null $clientSecret
	 * @param null $accessToken
	 * @param null $accessExpiresIn
	 * @throws KalturaAPIException
	 */
	public function __construct($webexBaseURL, $refreshToken = null, $clientId = null,
								$clientSecret= null, $accessToken = null, $accessExpiresIn = null)
	{
		if ($refreshToken == null && $accessToken == null)
		{
			throw new KalturaAPIException (KalturaZoomErrors::UNABLE_TO_AUTHENTICATE);
		}
		
		$this->zoomBaseURL = $webexBaseURL;
		$this->refreshToken = $refreshToken;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->accessToken = $accessToken;
		$this->accessExpiresIn = $accessExpiresIn;
		$this->zoomTokensHelper = new kZoomTokens($webexBaseURL, $clientId, $clientSecret);
	}
}
