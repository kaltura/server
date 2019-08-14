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
		$url = self::removeTokenWord($url);
		list($urlToToken, $restUrl) = self::splitUrlFlavorParts($url);
		return $this->calculateToken($urlToToken) . $restUrl;
	}

	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		$flavorRestUrl = '';
		$commonPartUrl = '';
		if (count($flavors) > 1)
		{
			if (isset($flavors[0][self::URL]))
			{
				$url = self::removeTokenWord($flavors[0][self::URL]);
				list($commonPartUrl, $flavorRestUrl) = self::splitUrlPartsFlavorsList($url);
			}
		}
		else if (count($flavors) == 1)
		{
			if (isset($flavors[0][self::URL]))
			{
				$url = self::removeTokenWord($flavors[0][self::URL]);
				list($commonPartUrl,$flavorRestUrl) = self::splitUrlFlavorParts($url);
			}
		}
		$urlTokenized = $this->calculateToken($commonPartUrl);
		self::setFlavorsUrlWithToken($flavors, $urlTokenized, $flavorRestUrl);
	}

	public static function setFlavorsUrlWithToken(&$flavors, $urlTokenized, $flavorRestUrl)
	{
		if ($urlTokenized === '')
		{
			return;
		}
		foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor[self::URL]))
			{
				if ($flavorRestUrl !== '')
				{
					$restUrl = $flavorRestUrl;
				}
				else
				{
					$restUrl = self::getRestUrl($flavor);
				}
				$flavors[$flavorKey][self::URL] = $urlTokenized . $restUrl;
			}
		}
	}

	public static function getRestUrl($flavor)
	{
		$restUrl = '';
		$flavorIdPos = strpos($flavor[self::URL], self::FLAVOR_ID . self::SLASH);
		if ($flavorIdPos !== false)
		{
			$restUrl = substr($flavor[self::URL], $flavorIdPos + strlen(self::FLAVOR_ID . self::SLASH));
		}
		return $restUrl;
	}

	public function calculateToken($urlToToken)
	{
		if ($urlToToken === '')
		{
			return '';
		}
		$nva = time() + $this->window;
		$path = self::SLASH . trim($urlToToken, self::SLASH) . self::SLASH;
		$dirs = substr_count($path, self::SLASH) - 1;
		$tokenParams = self::NVA . self::EQUAL . $nva . self::AMPERSAND . self::DIRS . self::EQUAL . $dirs;
		$uri = $path. self::QUESTION_MARK . $tokenParams;
		$hash = $this->gen . substr(hash_hmac(self::SECURE_HASH_ALGO, $uri, $this->key), 0, 20);
		$tokenParams .= self::AMPERSAND . self::HASH . self::EQUAL . $hash;
		$token = str_replace(self::AMPERSAND, self::TILDA , $tokenParams);
		return self::TOKEN . self::EQUAL . $token . $path;
	}

	public static function removeTokenWord($url)
	{
		$tokenPos = strpos($url, self::SLASH . self::TOKEN);
		if ($tokenPos !==  false)
		{
			$url = substr($url, $tokenPos + strlen(self::SLASH . self::TOKEN));
		}
		return $url;
	}

	/*
	 * In this case the flavor url has one flavor,
	 * the tokenize url should be till the last slash Occurrence
	 * splitting url into $urlToTokenize and $restUrl
	 * $urlToTokenize: until the last slash Occurrence
	 * $restUrl: from the last slash Occurrence
	 */
	public static function splitUrlFlavorParts($url)
	{
		$lastSlashOccurrence = strrpos ($url, self::SLASH) + 1;
		if ($lastSlashOccurrence === false)
		{
			return array('', '');
		}
		$restUrl = substr($url, $lastSlashOccurrence);
		$urlToTokenize = substr($url, 0, $lastSlashOccurrence);
		return array($urlToTokenize, $restUrl);
	}

	/*
	 * In this case the flavor url has several flavors,
	 * the tokenize url should be till the 'flavorId/'
	 * splitting url into $commonPartUrl and $restUrl
	 * $commonPartUrl: until the 'flavorId/'
	 * $restUrl: from the 'flavorId/'
	 */
	public static function splitUrlPartsFlavorsList($url)
	{
		$commonPartUrl = $url;
		$flavorIdPos = strpos($url, self::FLAVOR_ID . self::SLASH);
		if ($flavorIdPos !== false)
		{
			$commonPartUrl = substr($url, 0, $flavorIdPos) . self::FLAVOR_ID . self::SLASH;
		}
		$restUrl = '';
		return array($commonPartUrl, $restUrl);
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