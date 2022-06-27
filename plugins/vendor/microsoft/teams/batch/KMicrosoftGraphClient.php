<?php

/**
 * @package plugins.microsoftTeamsDropFolder
 * @subpackage batch
 */
class KMicrosoftGraphClient
{
	const AUTH_URL = 'https://login.microsoftonline.com/{tenantId}/oauth2/token';

	const TEAMS_APPLICATION_VALUE = 'Teams';

	const MEETING_SOURCE_VALUE = 'Meeting';

	public $apiUrl;

	public $tenantId;

	public $clientSecret;

	public $clientId;

	public $bearerToken;

	public $bearerTokenExpiry;

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
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$curlResponse = curl_exec($ch);
		curl_close($ch);
		if (!$curlResponse)
		{
			return false;
		}

		$response = json_decode($curlResponse, true);


		KalturaLog::info('Auth token generated: [' . $response[MicrosoftGraphFieldNames::ACCESS_TOKEN] . '], expiry: ' . date('c', $response[MicrosoftGraphFieldNames::EXPIRES_ON]));
		$this->bearerToken = $response[MicrosoftGraphFieldNames::ACCESS_TOKEN];
		$this->bearerTokenExpiry = $response[MicrosoftGraphFieldNames::EXPIRES_ON];

		return true;
	}

	public function getCallRecord($callRecordId)
	{
		$service = $this->apiUrl . "/v1.0/communications/callRecords/$callRecordId";
		return $this->sendGraphRequest($service);
	}

	public function getUser($userId)
	{
		$service = $this->apiUrl . "/v1.0/users/$userId";
		return $this->sendGraphRequest($service);
	}

	public function getDriveItem($driveId, $driveItemId)
	{
		$service = $this->apiUrl .  "/v1.0/drives/$driveId/items/$driveItemId";
		$response = $this->sendGraphRequest($service);

		if ($response)
		{
			if (isset ($response[MicrosoftGraphFieldNames::SOURCE]) &&
				$response[MicrosoftGraphFieldNames::SOURCE][MicrosoftGraphFieldNames::APPLICATION] === self::TEAMS_APPLICATION_VALUE &&
				isset ($response[MicrosoftGraphFieldNames::MEDIA]) && isset ($response[MicrosoftGraphFieldNames::MEDIA][MicrosoftGraphFieldNames::MEDIA_SOURCE])
				&& isset ($response[MicrosoftGraphFieldNames::MEDIA][MicrosoftGraphFieldNames::MEDIA_SOURCE][MicrosoftGraphFieldNames::CONTENT_CATEGORY]) && $response[MicrosoftGraphFieldNames::MEDIA][MicrosoftGraphFieldNames::MEDIA_SOURCE][MicrosoftGraphFieldNames::CONTENT_CATEGORY] === self::MEETING_SOURCE_VALUE)
			{
				return $response;
			}
			else
			{
				KalturaLog::info("Drive item $driveItemId is not a recorded meeting, and will be ignored.");
				return null;
			}
		}
	}

	public function sendGraphRequest ($url, $requestType = 'GET', $parameters = array(), $contentType = null)
	{
		if (!$this->bearerToken || $this->bearerTokenExpiry < time())
		{
			$authResult = $this->authenticate();
			if (!$authResult)
			{
				KalturaLog::info('Graph API authentication request could not be completed.');
				return null;
			}
		}

		$serviceUrl = $url;
		$ch = curl_init($serviceUrl);

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