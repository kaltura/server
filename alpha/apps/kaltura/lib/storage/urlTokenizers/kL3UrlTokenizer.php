<?php

class kL3UrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	private $gen = '';

	/**
	 * @var string
	 */
	public $paramName = '';

	/**
	 * @param array $urls
	 * @return string
	 */
	protected function getCommonPrefix(array $urls)
	{
		require_once( dirname(__FILE__). '/../../../../../../infra/general/kString.class.php');
		$acl = kString::getCommonPrefix($urls);

		$commaPos = strpos($acl, ','); // the first comma denotes the beginning of the non-common URL part
		if ($commaPos !== false)
		{
			$acl = substr($acl, 0, $commaPos);
		}
		return $acl;
	}

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$commonPrefix = $this->getCommonPrefix(array($url));
		if ($commonPrefix === '')
		{
			return $url;
		}
		$urlToTokenize = self::subStringLastSlash($commonPrefix);
		$path = '/' . trim($url,  '/');
		return $this->generateToken($urlToTokenize) . $path;
	}

	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		$urls = array();
		foreach($flavors as $flavor)
		{
			$urls[] = $flavor['url'];
		}
		$commonPrefix = $this->getCommonPrefix($urls);
		if ($commonPrefix === '')
		{
			return;
		}
		$urlToTokenize = self::subStringLastSlash($commonPrefix);
		$urlTokenized = $this->generateToken($urlToTokenize);
		foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor['url']))
			{
				$path = '/' . trim($flavor['url'], '/');
				$flavors[$flavorKey]['url'] = $urlTokenized . $path;
			}
		}
	}

	public function generateToken($urlToToken)
	{
		if ($urlToToken === '')
		{
			return '';
		}
		$nva = time() + $this->window;
		$path = '/' . trim($urlToToken,  '/') . '/';
		$dirs = substr_count($path,  '/') - 1;
		$tokenParams = "nva=$nva&dirs=$dirs";
		$uri = "$path?$tokenParams";
		$hash = $this->gen . substr(hash_hmac('sha1', $uri, $this->key), 0, 20);
		$tokenParams .= "&hash=$hash";
		$token = str_replace('&', '~', $tokenParams);
		return $this->paramName .'='. $token;
	}

	/*
	 * find the last slash occurrence
	 * return sub string of url till that slash
	 */
	public static function subStringLastSlash($url)
	{
		$lastSlashOccurrence = strrpos ($url, '/');
		if ($lastSlashOccurrence === false)
		{
			return '';
		}
		$urlToTokenize = substr($url, 0, $lastSlashOccurrence + 1);
		return $urlToTokenize;
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

	/**
	 * @return string $paramName
	 */
	public function getParamName()
	{
		return $this->paramName;
	}

	/**
	 * @param string $paramName
	 */
	public function setParamName($paramName)
	{
		$this->paramName = $paramName;
	}
}