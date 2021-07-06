<?php


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

	public function authenticate ()
	{
		$url = str_replace('{tenantId}', $this->tenantId, self::AUTH_URL);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$fields = array('grant_type' => 'client_credentials', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'resource' => $this->apiUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_close($ch);

		$response = json_decode(curl_exec($ch), true);

		KalturaLog::info('Auth token generated: [' . $response['access_token'] . '], expiry: ' . date('c', $response['expires_on']));
		$this->bearerToken = $response['access_token'];
	}

	public function listSites ()
	{
		$service = 'sites';
		return $this->sendGraphRequest($service);
	}

	public function listUsers ()
	{
		$service = 'users';
		return $this->sendGraphRequest($service);
	}

	public function listDrives ($siteId)
	{
		$service = "sites/$siteId/drives";
	}

	public function listRecentDriveItems ($driveId)
	{
		$service = "drives/$driveId/root/delta";
		do{
			$response = $this->sendGraphRequest($service);

		}while (isset($response['nextLink']));



	}

	public function getDriveItem($driveId, $driveItemId)
	{
		$service = "drives/$driveId/items/$driveItemId";
		return $this->sendGraphRequest($service);
	}

	protected function sendGraphRequest ($service, $requestType = 'GET', $parameters = array(), $contentType = null)
	{
		$serviceUrl = $this->apiUrl . "/$service";
		$ch = curl_init($serviceUrl);

		$authHeader = "Authorization: Bearer {$this->bearerToken}";
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader));
		$result = curl_exec($ch);
		curl_close($ch);

		return json_decode($result, true);
	}
}