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
	 * @return array
	 * @throws Exception
	 */
	public function refreshTokens($oldRefreshToken)
	{
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		$userPwd = "$clientId:$clientSecret";
		$postFields = "grant_type=refresh_token&refresh_token=$oldRefreshToken";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = $this->parseTokens($response);
		$this->saveNewTokenDataToZoom($tokensData, $clientId);
		return $tokensData;
	}

	/**
	 * @param bool $forceNewToken
	 * @return array
	 * @throws Exception
	 */
	public function retrieveTokensData($forceNewToken = false)
	{
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$zoomClientData = VendorIntegrationPeer::retrieveSingleVendorPerAccountAndType($clientId, VendorTypeEnum::ZOOM_CONFIGURATION);
		if ($zoomClientData && !$forceNewToken) // tokens exist
		{
			if (time() > $zoomClientData->getExpiresIn()) // token had expired -> refresh
			 	return $this->refreshTokens($zoomClientData->getRefreshToken());
			return array(self::ACCESS_TOKEN => $zoomClientData->getAccessToken(), self::REFRESH_TOKEN => $zoomClientData->getRefreshToken(),
				self::EXPIRES_IN => $zoomClientData->getExpiresIn());
		}
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		$userPwd = "$clientId:$clientSecret";
		$postFields = "grant_type=authorization_code&code={$_GET['code']}&redirect_uri=$redirectUrl";
		$response = $this->curlRetrieveTokensData($zoomBaseURL, $userPwd, $header, $postFields);
		$tokensData = $this->parseTokens($response);
		$this->saveNewTokenDataToZoom($tokensData, $clientId);
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
		KalturaLog::info('Calling zoom auth');
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_USERPWD, $userPwd);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		$response = $curlWrapper->exec($url . self::OAUTH_TOKEN_PATH);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return $response;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function setValidUntil($data)
	{
		$expiresIn = $data[self::EXPIRES_IN];
		$data[self::EXPIRES_IN] = time() + $expiresIn - 120;
		return $data;
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
	 * @param $dataAsArray
	 * @param $clientId
	 * @throws PropelException
	 */
	private function saveNewTokenDataToZoom($dataAsArray, $clientId)
	{
		$zoomClientConfiguration = new VendorIntegration();
		$zoomClientConfiguration->setExpiresIn($dataAsArray[self::EXPIRES_IN]);
		$zoomClientConfiguration->setAccessToken($dataAsArray[self::ACCESS_TOKEN]);
		$zoomClientConfiguration->setRefreshToken($dataAsArray[self::REFRESH_TOKEN]);
		$zoomClientConfiguration->setAccountId($clientId);
		$zoomClientConfiguration->setVendorType(VendorTypeEnum::ZOOM_CONFIGURATION);
		$zoomClientConfiguration->save();
	}

	/**
	 * @param $response
	 * @return array
	 */
	private function parseTokens($response)
	{
		$dataAsArray = json_decode($response, true);
		$dataAsArray = $this->setValidUntil($dataAsArray);
		return $this->extractTokensFromResponse($dataAsArray);
	}
}