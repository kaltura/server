<?php

/**
 * @package plugins.microsoftTeamsDropFolder
 * @subpackage batch
 */
class KMicrosoftGraphClient
{
	const AUTH_URL = 'https://login.microsoftonline.com/{tenantId}/oauth2/token';

	public $apiUrl;

	public $tenantId;

	public $clientSecret;

	public $clientId;

	public $bearerToken;


	function __construct($tenantId, $apiUrl, $clientId, $clientSecret)
	{
		$this->apiUrl = $apiUrl;
		$this->tenantId = $tenantId;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
	}

	public function refreshToken ($refreshToken, $scope)
	{
		$url = str_replace('{tenantId}', $this->tenantId, self::AUTH_URL);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$fields = array('grant_type' => 'refresh_token', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'scope' => $scope, 'refresh_token' => $refreshToken);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$curlResponse = curl_exec($ch);
		curl_close($ch);
		if (!$curlResponse)
		{
			return false;
		}

		$response = json_decode($curlResponse, true);

		KalturaLog::info('Auth token generated: [' . $response[MicrosoftGraphFieldNames::ACCESS_TOKEN] . '], expiry: ' . date('c', $response[MicrosoftGraphFieldNames::EXPIRES_ON]));
		$this->bearerToken = $response['access_token'];

		return [
			$response['access_token'],
			$response['refresh_token'],
			$response['expires_on'],
			];
	}

	public function getUser()
	{
		$service = "me";
		return $this->sendGraphRequest($service);
	}

	public function getDriveItem($driveId, $driveItemId)
	{
		$service = "drives/$driveId/items/$driveItemId";
		$response = $this->sendGraphRequest($service);

		if ($response)
		{
        	return $response;
		}

		return null;
	}

	public function sendGraphRequest ($serviceUri, $requestType = 'GET', $parameters = array(), $contentType = null)
	{
		$ch = curl_init("{$this->apiUrl}/$serviceUri");

		$authHeader = "Authorization: Bearer {$this->bearerToken}";
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader));
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$result = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//TODO add handling for 427 and 503 errors.

		curl_close($ch);

		if ($responseCode && $responseCode == 200 && $result)
		{
			return json_decode($result, true);
		}
		else
		{
			KalturaLog::info("Error occurred executing Graph API call: $result");
			return null;
		}


	}
}