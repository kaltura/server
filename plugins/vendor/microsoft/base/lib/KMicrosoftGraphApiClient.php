<?php

/**
 * @package plugins.Microsoft
 * @subpackage lib
 */
class KMicrosoftGraphApiClient
{
	const AUTH_URL = 'https://login.microsoftonline.com/{tenantId}/oauth2/token';

	const TEAMS_APPLICATION_VALUE = 'Teams';

	const MEETING_SOURCE_VALUE = 'Meeting';

	const API_URL = 'https://graph.microsoft.com';

	public $tenantId;

	public $clientSecret;

	public $clientId;

	public $bearerToken;

	protected $bearerTokenExpiry;

	function __construct($tenantId, $clientId, $clientSecret)
	{
		$this->tenantId = $tenantId;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
	}

	public function authenticate()
	{
		$url = str_replace('{tenantId}', $this->tenantId, self::AUTH_URL);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$fields = array('grant_type' => 'client_credentials', 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'resource' => self::API_URL);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch), true);
		curl_close($ch);

		if (isset($response[MicrosoftGraphFieldNames::ACCESS_TOKEN]))
		{
			KalturaLog::info('Auth token generated: [' . $response[MicrosoftGraphFieldNames::ACCESS_TOKEN] . '], expiry: ' . date('c', $response[MicrosoftGraphFieldNames::EXPIRES_ON]));
			$this->bearerToken = $response[MicrosoftGraphFieldNames::ACCESS_TOKEN];
			$this->bearerTokenExpiry = $response[MicrosoftGraphFieldNames::EXPIRES_ON];
		}
		else
		{
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($responseCode)
			{
				KalturaLog::info("Error occurred while generating Graph API authentication token (Code $responseCode): $response");
			}
			else
			{
				KalturaLog::info("Error occurred while generating Graph API authentication token: no response");
			}
		}
	}
	
	public function getTokenExpiry()
	{
		return $this->bearerTokenExpiry;
	}

	public function getCallRecord($callRecordId)
	{
		$service = self::API_URL . "/v1.0/communications/callRecords/$callRecordId";
		return $this->sendGraphRequest($service);
	}

	public function getUser($userId)
	{
		$service = self::API_URL . "/v1.0/users/$userId";
		return $this->sendGraphRequest($service);
	}
	
	public function getUserByMail($email)
	{
		$service = self::API_URL . "/v1.0/users?\$filter=startsWith(mail,'$email')";
		return $this->sendGraphRequest($service);
	}
	
	public function getDriveDeltaPage($userTeamsId)
	{
		$service = self::API_URL . "/v1.0/users/$userTeamsId/drive/root/children?select=id,name,specialFolder";
		return $this->sendGraphRequest($service);
	}
	
	public function getRecordingFolderDeltaPage($userTeamsId, $recordingsFolderId)
	{
		$service = self::API_URL . "/v1.0/users/$userTeamsId/drive/items/$recordingsFolderId/delta";
		return $this->sendGraphRequest($service);
	}

	public function getDriveItem($driveId, $driveItemId)
	{
		$service = self::API_URL . "/v1.0/drives/$driveId/items/$driveItemId";
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
			$this->authenticate();
		}
		KalturaLog::info("Sending graph request - $url");
		$serviceUrl = $url;
		$ch = curl_init($serviceUrl);

		$authHeader = "Authorization: Bearer {$this->bearerToken}";
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader));
		$result = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//TODO add handling for 427 and 503 errors.

		curl_close($ch);

		if ($responseCode == 200)
		{
			return json_decode($result, true);
		}
		else
		{
			KalturaLog::info("Error occurred executing Graph API call (Code $responseCode): $result");
			return null;
		}


	}
}