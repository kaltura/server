<?php

/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionGoogleClientHelper
{
	/**
	 * @var Google_Client
	 */
	private static $_googleClient;

	/**
	 * @var Google_YouTubeService
	 */
	private static $_youtubeService;

	public static function getGoogleClient($clientId, $clientSecret)
	{
		if (!is_null(self::$_googleClient))
			return self::$_googleClient;

		// add google client library to include path
		set_include_path(get_include_path().PATH_SEPARATOR.KALTURA_ROOT_PATH.'/vendor/google-api-php-client-1.1.2/src/Google');
		require_once 'autoload.php';
		require_once 'Client.php';


		$client = new Google_Client();
		$client->setClientId($clientId);
		$client->setClientSecret($clientSecret);

		self::$_googleClient = $client;
		return $client;
	}

	public static function getYouTubeService($clientId, $clientSecret, $accessToken)
	{
		if (!is_null(self::$_youtubeService))
			return self::$_youtubeService;

		$client = self::getGoogleClient($clientId, $clientSecret);
		$accessTokenObject = json_decode($accessToken);
		if (isset($accessTokenObject->refresh_token))
			$client->refreshToken($accessTokenObject->refresh_token);
		else
			$client->setAccessToken($accessToken);
		$service = new Google_YouTubeService($client);

		self::$_youtubeService = $service;
		return $service;
	}
}