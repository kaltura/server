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
			//in case of a cached-in tokenizer - need to require bootstrap
			require_once( dirname(__FILE__). '/../../../../../' . 'bootstrap.php');
			
			$strings = array();
			foreach($flavors as $flavor)
			{
				$url = $flavor["url"];
				if (substr($url, 0, 4) == "mp4:")
					$url = substr($url, 4);
				$strings[] = $url;
			}
			$prefix = kString::getCommonPrefix($strings);
			$pos = strrpos($prefix , "/");

			if ($pos)
			{
				//include slash sign in prefix substr
				$pos++;
				$prefix = substr($prefix, 0, $pos);
			}
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
