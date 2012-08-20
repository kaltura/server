<?php

require_once(dirname(__file__) . '/../../../../../../infra/akamai/token/StreamTokenFactory.php');

class kAkamaiRtmpUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $profile;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $salt;

	/**
	 * @var int
	 */
	protected $window;

	/**
	 * @var string
	 */
	protected $aifp;

	/**
	 * @var bool
	 */
	protected $usePrefix;

	/**
	 * @param string $profile
	 * @param string $type
	 * @param string $salt
	 * @param int $window
	 * @param string $aifp
	 * @param bool $usePrefix
	 */
	public function __construct($profile, $type, $salt, $window, $aifp, $usePrefix)
	{
		$this->profile = $profile;
		$this->type = $type;
		$this->salt = $salt;
		$this->window = $window;
		$this->aifp = $aifp;
		$this->usePrefix = $usePrefix;
	}
	
	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		if ($this->usePrefix)
		{
			$urls = array();
			$minLen = 1024;

			foreach($flavors as $flavor)
			{
				$url = $flavor["url"];
				if (substr($url, 0, 4) == "mp4:")
					$url = substr($url, 4);
				$urls[] = $url;

				$minLen = min($minLen, strlen($url));
			}

			$url = array_pop($urls);

			$scan = true;
			for($i = 0; $i < $minLen && $scan; $i++)
			{
				$c = substr($url, $i, 1);
				foreach($urls as $url)
				{
					if ($c != substr($url, $i, 1))
					{
						$scan = false;
						break;
					}
				}
			}

			$prefix = substr($url, 0, $i - 1);
		}
		else
		{
			$prefix = "";
			foreach($flavors as $flavor)
			{
				$url = $flavor["url"];
				if (substr($url, 0, 4) == "mp4:")
					$url = substr($url, 4);
				$prefix = $prefix . $url . ";";
			}
		}

		$factory = new StreamTokenFactory;
		$token = $factory->getToken($this->type, $prefix, null, $this->profile, $this->salt, null, $this->window, null, null, null);
		$auth = "?auth=".$token->getToken()."&aifp={$this->aifp}&slist=$prefix";
		$baseUrl .= $auth;
		foreach($flavors as &$flavor)
		{
			$url = $flavor["url"];
			$flavor["url"] = $url.$auth;
		}
	}
}
