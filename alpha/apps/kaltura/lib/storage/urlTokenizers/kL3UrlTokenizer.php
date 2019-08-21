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
	protected function getAcl(array $urls)
	{
		require_once( dirname(__FILE__). '/../../../../../../infra/general/kString.class.php');
		$acl = kString::getCommonPrefix($urls);

		$commaPos = strpos($acl, ','); // the first comma denotes the beginning of the non-common URL part
		if ($commaPos !== false)
		{
			$acl = substr($acl, 0, $commaPos);
		}

		$lastSlashOccurrence = strrpos ($acl, '/');
		if ($lastSlashOccurrence === false)
		{
			return '';
		}
		$acl = substr($acl, 0, $lastSlashOccurrence + 1);
		return $acl;
	}

	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$acl = $this->getAcl(array($url));
		if ($acl === '')
		{
			return $url;
		}
		$path = '/' . ltrim($url,  '/');
		return $this->generateToken($acl) . $path;
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
		$acl = $this->getAcl($urls);
		if ($acl === '')
		{
			return;
		}
		$token = $this->generateToken($acl);
		foreach($flavors as $flavorKey => $flavor)
		{
			if (isset($flavor['url']))
			{
				$path = '/' . ltrim($flavor['url'], '/');
				$flavors[$flavorKey]['url'] = $token . $path;
			}
		}
	}

	public function generateToken($acl)
	{
		if ($acl === '')
		{
			return '';
		}
		$nva = time() + $this->window;
		$path = '/' . trim($acl,  '/') . '/';
		$dirs = substr_count($path,  '/') - 1;
		$tokenParams = "nva=$nva&dirs=$dirs";
		$uri = "$path?$tokenParams";
		$hash = $this->gen . substr(hash_hmac('sha1', $uri, $this->key), 0, 20);
		$tokenParams .= "&hash=$hash";
		$token = str_replace('&', '~', $tokenParams);
		return $this->paramName .'='. $token;
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