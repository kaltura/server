<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoomOauth
 */
class kZoomOauth implements kVendorOauth
{
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const TOKEN_TYPE = 'token_type';
	const EXPIRES_IN = 'expires_in';
	const SCOPE = 'scope';


	/**
	 * @param string $oldRefreshToken
	 * @param VendorIntegration $vendorIntegration
	 * @return array
	 * @throws Exception
	 */
	public function refreshTokens($oldRefreshToken, $vendorIntegration)
	{
		KalturaLog::info('Refreshing Tokens');
		list($zoomBaseURL, , $header, $userPwd) = $this->getZoomHeaderData();
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = $this->parseTokens($response);
		return $tokensData;
	}

	/**
	 * @param bool $forceNewToken
	 * @param string $accountId
	 * @return array
	 * @throws Exception
	 */
	public function retrieveTokensData($forceNewToken = false, $accountId = null)
	{
		KalturaLog::info('Retrieving Tokens');
		$zoomIntegration = null;
		if (!$forceNewToken && $accountId)
		{
			$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
			if ($zoomIntegration) // tokens exist
			{
				if (time() > $zoomIntegration->getExpiresIn()) // token had expired -> refresh
					return $this->refreshTokens($zoomIntegration->getRefreshToken(), $zoomIntegration);
				return array(self::ACCESS_TOKEN => $zoomIntegration->getAccessToken(), self::REFRESH_TOKEN => $zoomIntegration->getRefreshToken(),
					self::EXPIRES_IN => $zoomIntegration->getExpiresIn());
			}
		}
		list($zoomBaseURL, $redirectUrl, $header, $userPwd) = $this->getZoomHeaderData();
		$postFields = "grant_type=authorization_code&code={$_GET['code']}&redirect_uri=$redirectUrl";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = $this->parseTokens($response);
		return $tokensData;
	}

	/**
	 * @param $url
	 * @param $userPwd
	 * @param $header
	 * @param $postFields
	 * @return mixed|string
	 * @throws Exception
	 */
	private function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_USERPWD, $userPwd);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		return $curlWrapper->exec($url . self::OAUTH_TOKEN_PATH);
	}

	/**
	 * set two minutes off the token expiration, avoid 401 response from zoom
	 * @param int $expiresIn
	 * @return int
	 */
	private function setValidUntil($expiresIn)
	{
		return time() + $expiresIn - 120;
	}

	/**
	 * @param array $data
	 * @return array<tokens>
	 */
	private function extractTokensFromResponse($data)
	{
		return array(self::ACCESS_TOKEN => $data[self::ACCESS_TOKEN], self::REFRESH_TOKEN => $data[self::REFRESH_TOKEN],
			self::EXPIRES_IN => $data[self::EXPIRES_IN]);
	}

	/**
	 * @param $response
	 * @return array
	 * @throws Exception
	 */
	private function parseTokens($response)
	{
		$dataAsArray = json_decode($response, true);
		if (!$dataAsArray)
		{
			KalturaLog::err('Parse Tokens failed, response received from zoom is: ' . $response);
			throw new KalturaAPIException("Unable To parse Tokens please check zoom configuration");
		}
		$expiresIn = $dataAsArray[self::EXPIRES_IN];
		$dataAsArray[self::EXPIRES_IN] = $this->setValidUntil($expiresIn);
		return $this->extractTokensFromResponse($dataAsArray);
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	private function getZoomHeaderData()
	{
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		$userPwd = "$clientId:$clientSecret";
		return array($zoomBaseURL, $redirectUrl, $header, $userPwd);
	}
}