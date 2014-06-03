<?php

require_once(dirname(__file__) . '/../../../../../../vendor/akamai/token/StreamTokenFactory.php');

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
	protected $aifp;

	/**
	 * @var bool
	 */
	protected $usePrefix;

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
		$token = $factory->getToken($this->type, $prefix, null, $this->profile, $this->key, null, $this->window, null, null, null);
		$auth = "?auth=".$token->getToken()."&aifp={$this->aifp}&slist=$prefix";
		$baseUrl .= $auth;
		foreach($flavors as &$flavor)
		{
			$url = $flavor["url"];
			$flavor["url"] = $url.$auth;
		}
	}
	
	/**
	 * @return the $profile
	 */
	public function getProfile() {
		return $this->profile;
	}

	/**
	 * @return the $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return the $aifp
	 */
	public function getAifp() {
		return $this->aifp;
	}

	/**
	 * @return the $usePrefix
	 */
	public function getUsePrefix() {
		return $this->usePrefix;
	}

	/**
	 * @param string $profile
	 */
	public function setProfile($profile) {
		$this->profile = $profile;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param string $aifp
	 */
	public function setAifp($aifp) {
		$this->aifp = $aifp;
	}

	/**
	 * @param boolean $usePrefix
	 */
	public function setUsePrefix($usePrefix) {
		$this->usePrefix = $usePrefix;
	}

	
	
}
