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
		return kString::getCommonPrefix($urls);
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
		list($urlToTokenize, $restUrl) = self::splitUrlParts($commonPrefix);
		return $this->generateToken($urlToTokenize) . $restUrl;
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
		list($urlToTokenize, $restUrlOneFlavor) = self::splitUrlParts($commonPrefix);
		$urlTokenized = $this->generateToken($urlToTokenize);
		if ($urlTokenized === '')
		{
			return;
		}
		self::setFlavorsUrlWithToken($flavors, $urlTokenized, $restUrlOneFlavor);
	}

	public static function setFlavorsUrlWithToken(&$flavors, $urlTokenized, $restUrlOneFlavor)
	{
		foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor['url']))
			{
				if (count($flavors) == 1)
				{
					$restUrl = $restUrlOneFlavor;
				}
				else
				{
					$restUrl = self::getRestUrl($flavor);
				}
				$flavors[$flavorKey]['url'] = $urlTokenized . $restUrl;
			}
		}
	}


	public static function getRestUrl($flavor)
	{
		$restUrl = '';
		$flavorIdPos = strpos($flavor['url'], 'flavorId/');
		if ($flavorIdPos !== false)
		{
			$restUrl = substr($flavor['url'], $flavorIdPos + strlen('flavorId/'));
		}
		return $restUrl;
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
		return $this->paramName .'='. $token . $path;
	}

	/*
	 * splitting url into $urlToTokenize and $restUrl
	 * $urlToTokenize: until the last slash Occurrence
	 * $restUrl: from the last slash Occurrence
	 */
	public static function splitUrlParts($url)
	{
		$lastSlashOccurrence = strrpos ($url, '/');
		if ($lastSlashOccurrence === false)
		{
			return array('', '');
		}
		$restUrl = substr($url, $lastSlashOccurrence + 1);
		$urlToTokenize = substr($url, 0, $lastSlashOccurrence + 1);
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