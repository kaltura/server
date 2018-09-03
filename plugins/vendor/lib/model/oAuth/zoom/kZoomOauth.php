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
	 * @param string $response
	 * @return array<tokens>
	 */
	public function extractTokensFromResponse($response)
	{
		$dataArray = json_decode($response, true);
		return array(self::ACCESS_TOKEN => $dataArray[self::ACCESS_TOKEN], self::REFRESH_TOKEN => $dataArray[self::REFRESH_TOKEN]);
	}

	/**
	 * @param string $oldRefreshToken
	 * @return string newAccessToken
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
		$response = $this->curlRetrieveData($zoomBaseURL, $userPwd, $header, $postFields);
		return $response;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function retrieveTokensData()
	{
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$redirectUrl = $zoomConfiguration['redirectUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array('Content-Type:application/x-www-form-urlencoded');
		$userPwd = "$clientId:$clientSecret";
		$postFields = "grant_type=authorization_code&code={$_GET['code']}&redirect_uri=$redirectUrl";
		$response = $this->curlRetrieveData($zoomBaseURL, $userPwd, $header, $postFields);
		return $response;
	}

	/**
	 * @param $url
	 * @param $userPwd
	 * @param $header
	 * @param $postFields
	 * @return mixed|string
	 * @throws Exception
	 */
	private function curlRetrieveData($url, $userPwd, $header, $postFields)
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
		//if ($httpCode === 400 || $httpCode === 401)
		//	$response = $this->refreshTokens();
		if (!$response || $httpCode !== 200 || $curlWrapper->getError())
		{
			KalturaLog::err('Zoom Curl returned error, Tokens were not received, Error: ' . $curlWrapper->getError());
			KExternalErrors::dieGracefully();
		}
		return $response;
	}
}