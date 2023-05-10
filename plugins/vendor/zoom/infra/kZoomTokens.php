<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kZoomTokens
{
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const MAP_NAME = 'vendor';
	const OAUTH_TOKEN_PATH = '/oauth/token';
	const ACCESS_TOKEN = 'access_token';
	const REFRESH_TOKEN = 'refresh_token';
	const AUTHORIZATION_HEADER = 'Authorization';
	const EXPIRES_IN = 'expires_in';
	const SCOPE = 'scope';
	
	protected $zoomBaseURL;
	protected $accountId;
	protected $zoomAuthType;

	public function __construct($zoomBaseURL, $accountId, $zoomAuthType)
	{
		$this->zoomBaseURL = $zoomBaseURL;
		$this->accountId = $accountId;
		$this->zoomAuthType = $zoomAuthType;
	}

	public function generateServerToServerToken()
	{
		KalturaLog::info('Generating new Zoom serve-to-server access token');
		$postFields = "grant_type=account_credentials&account_id=" . $this->accountId;
		$header = self::getHeaderData();
		$response = self::curlRetrieveTokensData($postFields, $header);
		$tokensData = $this->parseTokensResponse($response);
		KalturaLog::debug('New access token response' . print_r($tokensData, true));
		return $tokensData;
	}

	protected function parseTokensResponse($response)
	{
		$tokensData = json_decode($response, true);
		if (!$tokensData || !isset($tokensData[self::ACCESS_TOKEN]) || !isset($tokensData[self::EXPIRES_IN]))
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::TOKEN_PARSING_FAILED . print_r($tokensData, true));
		}
		return $tokensData;
	}

	protected function getHeaderData()
	{
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$clientId = $zoomConfiguration['clientId'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		$header = array(self::AUTHORIZATION_HEADER . ":Basic " . base64_encode("$clientId:$clientSecret"));
		return $header;
	}
	
	protected function curlRetrieveTokensData($postFields, $header)
	{
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HEADER, true);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		return $curlWrapper->exec($this->zoomBaseURL . self::OAUTH_TOKEN_PATH);
	}
	
}