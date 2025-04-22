<?php
/**
 * @package infra
 * @subpackage oauth
 */

class kFirebaseOauth
{
	const ACCESS_TOKEN = 'access_token';
	const EXPIRES_IN = 'expires_in';
	const TOKEN_EXPIRY_GRACE = 600;

	const URL = 'https://oauth2.googleapis.com/token';

	/**
	 * @param $authCode
	 * @return array|void
	 */
	public static function requestAuthorizationTokens($authCode)
	{
		$accessTokens = self::getTokensFromCache();
		if ($accessTokens)
		{
			KalturaLog::info("Retrieved tokens from cache");
			return $accessTokens;
		}

		KalturaLog::info('Requesting authorization tokens from Firebase');

		$header = self::getHeaderData();
		$jwt = self::createFirebaseJwt(self::URL);
		if (!$jwt)
		{
			return null;
		}
		$postFields = http_build_query(array('grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $jwt));

		$response = self::curlRetrieveTokensData(self::URL, null, $header, $postFields);
		$accessTokens = self::retrieveTokensDataFromResponse($response);
		if (!$accessTokens)
		{
			return null;
		}
		self::saveTokensToCache($accessTokens);

		return $accessTokens;
	}

	protected static function getTokensFromCache()
	{
		$cache = self::getCache();
		if (!$cache)
		{
			return null;
		}

		$accessTokens = $cache->get(self::getCacheKey());
		if (!self::validateTokens($accessTokens))
		{
			return null;
		}

		if ($accessTokens[kFirebaseOauth::EXPIRES_IN] > time() + self::TOKEN_EXPIRY_GRACE)
		{
			return $accessTokens;
		}

		return null;
	}

	protected static function saveTokensToCache($accessTokens)
	{
		$cache = self::getCache();
		if (!$cache)
		{
			return;
		}

		$cache->set(self::getCacheKey(), $accessTokens, kTimeConversion::HOUR);
	}

	protected static function getCache()
	{
		$memcacheConfig = kConf::get('memcacheLocal', 'cache', null);
		if (!$memcacheConfig)
		{
			KalturaLog::err("Failed to get memcache configuration");
			return null;
		}

		$memcache = new kInfraMemcacheCacheWrapper();
		if (!$memcache->init($memcacheConfig))
		{
			KalturaLog::err("Failed to connect to memcache");
			return null;
		}

		return $memcache;
	}

	protected static function getCacheKey()
	{
		return 'firebase_oauth_tokens';
	}

	/**
	 * @return array
	 */
	protected static function getHeaderData()
	{
		return array('Content-Type: application/x-www-form-urlencoded');
	}

	protected static function createFirebaseJwt($url)
	{
		$serviceAccountJson = self::getServiceAccountJson();
		if (!$serviceAccountJson)
		{
			KalturaLog::err('Error: Failed retrieving service account JSON');
			return null;
		}
		$serviceAccount = json_decode($serviceAccountJson, true);

		$header = base64_encode(json_encode([
			'alg' => 'RS256',
			'typ' => 'JWT'
		]));

		$now = time();
		$expiry = $now + 3600; // Token valid for 1 hour

		$payload = base64_encode(json_encode(array(
			'iss' => $serviceAccount['client_email'],
			'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
			'aud' => $url,
			'iat' => $now,
			'exp' => $expiry
		)));

		// Create the signature
		$signature = '';
		openssl_sign("$header.$payload", $signature, $serviceAccount['private_key'], 'SHA256');

		return "$header.$payload." . base64_encode($signature);
	}

	public static function validateTokens($tokensData)
	{
		if (!$tokensData || !isset($tokensData[self::ACCESS_TOKEN]) || !isset($tokensData[self::EXPIRES_IN]))
		{
			return false;
		}

		return true;
	}

	public static function extractTokensFromData($data)
	{
		return array(self::ACCESS_TOKEN => $data[self::ACCESS_TOKEN], self::EXPIRES_IN => $data[self::EXPIRES_IN]);
	}

	protected static function getServiceAccountJson()
	{
		return file_get_contents(dirname(__FILE__) . '/../../../configurations/firebase_service_account.json');
	}

	protected static function curlRetrieveTokensData($url, $userPwd, $header, $postFields)
	{
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $header);
		$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $postFields);
		$response = $curlWrapper->exec($url);

		return $response;
	}

	protected static function retrieveTokensDataFromResponse($response)
	{
		$tokensData = self::parseTokensResponse($response);
		if (!self::validateTokens($tokensData))
		{
			return null;
		}
		$tokensData = self::extractTokensFromData($tokensData);
		$tokensData[self::EXPIRES_IN] = self::getTokenExpiryRelativeTime($tokensData[self::EXPIRES_IN]);
		return $tokensData;
	}

	public static function parseTokensResponse($response)
	{
		$dataAsArray = json_decode($response, true);
		return $dataAsArray;
	}

	public static function getTokenExpiryRelativeTime($expiresIn)
	{
		$expiresIn = time() + $expiresIn - kTimeConversion::MINUTE * 2;
		KalturaLog::info("Set Token 'expires_in' to " . $expiresIn);
		return $expiresIn;
	}
}
