<?php

class kL3UrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	private $gen = '';

	const FLAVOR_ID = 'flavorId';
	const URL = 'url';
	const NVA = 'nva';
	const DIRS = 'dirs';
	const SECURE_HASH_ALGO ='sha1';
	const TOKEN = 'token';
	const HASH = 'hash';
	const SLASH = '/';
	const EQUAL = '=';
	const AMPERSAND = '&';
	const QUESTION_MARK = '?';
	const TILDA = '~';

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		return $this->tokenizeUrl($url);
	}

	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		if (count($flavors) > 1)
		{
			if (isset($flavors[0][self::URL]))
			{
				$commonPartUrl = $flavors[0][self::URL];
				$flavorIdPos = strpos($commonPartUrl, self::FLAVOR_ID . self::SLASH);
				if ($flavorIdPos !== false)
				{
					$urlToToken = substr($commonPartUrl,0, $flavorIdPos). self::FLAVOR_ID . self::SLASH ;
					$urlTokenized = $this->tokenizeUrl($urlToToken, true);
					self::setFlavorsUrlWithToken($flavors, $urlTokenized);
				}
			}
		}
		else if (count($flavors) == 1)
		{
			if (isset($flavors[0][self::URL]))
			{
				$flavors[0][self::URL] = $this->tokenizeUrl($flavors[0][self::URL]);
			}
		}
	}

	public static function setFlavorsUrlWithToken(&$flavors, $urlTokenized)
	{
		foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor[self::URL]))
			{
				$flavorIdPos = strpos($flavor[self::URL], self::FLAVOR_ID . self::SLASH);
				$restUrl = substr($flavors[$flavorKey][self::URL], $flavorIdPos + strlen(self::FLAVOR_ID . self::SLASH));
				$flavors[$flavorKey][self::URL] = $urlTokenized . $restUrl;
			}
		}
	}

	/**
	 * @param string $url
	 * @param boolean $isMultiUrls
	 * @return string
	 */
	public function tokenizeUrl($url, $isMultiUrls = false)
	{
		$url = self::removeTokenWord($url);
		if ($isMultiUrls == true)
		{
			$urlToToken = $url;
			$restUrl = '';
		}
		else
		{
			list($urlToToken, $restUrl) = self::getUrlToTokenize($url);
		}

		return $this->calculateToken($urlToToken, $restUrl);
	}

	public function calculateToken($urlToToken, $restUrl)
	{
		$nva = time() + $this->window;
		$path = self::SLASH . trim($urlToToken, self::SLASH) . self::SLASH;
		$dirs = substr_count($path, self::SLASH) - 1;
		$tokenParams = self::NVA . self::EQUAL . $nva . self::AMPERSAND . self::DIRS . self::EQUAL . $dirs;
		$uri = $path. self::QUESTION_MARK . $tokenParams;
		$hash = $this->gen . substr(hash_hmac(self::SECURE_HASH_ALGO, $uri, $this->key), 0, 20);
		$tokenParams .= self::AMPERSAND . self::HASH . self::EQUAL . $hash;
		$token = str_replace(self::AMPERSAND, self::TILDA , $tokenParams);
		return self::TOKEN . self::EQUAL . $token . $path . $restUrl;
	}

	public static function removeTokenWord($url)
	{
		$tokenPos = strpos($url, self::SLASH . self::TOKEN);
		$url = substr($url, $tokenPos + strlen(self::SLASH . self::TOKEN));
		return $url;
	}

	public static function getUrlToTokenize($url)
	{
		$lastSlashOccurrence = strrpos ($url, self::SLASH) + 1;
		$restUrl = substr($url, $lastSlashOccurrence);
		$urlToTokenize = substr($url, 0, $lastSlashOccurrence);
		return array($urlToTokenize, $restUrl);
	}

	/**
	 * @return string $gen
	 */
	public function getGen()
	{
		return $this->gen;
	}

	/**
	 * @param string $gen
	 */
	public function setGen($gen)
	{
		$this->gen = $gen;
	}
}