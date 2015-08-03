<?php

abstract class kUrlTokenizer
{
	
	/**
	 * @var int
	 */
	protected $window;
	
	/**
	 * @var string
	 */
	protected $key;
	
	/**
	 * @var string
	 */
	protected $playbackContext;
	
	/**
	 * @var kSessionBase
	 */
	protected $ksObject;
	
	/**
	 * @param string $playbackContext
	 */
	public function setPlaybackContext($playbackContext)
	{
		$this->playbackContext = $playbackContext;
	}
	
	/**
	 * @return string
	 */
	protected function getPlaybackContext()
	{
		return $this->playbackContext;
	}
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		return $url;
	}
	
	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
		foreach($flavors as &$flavor)
		{
			$flavor['url'] = $this->tokenizeSingleUrl($flavor['url']);
		}
	}
	
	/**
	 * @return the $window
	 */
	public function getWindow() {
		return $this->window;
	}

	/**
	 * @return the $key
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @param number $window
	 */
	public function setWindow($window) {
		$this->window = $window;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key) {
		$this->key = $key;
	}

	/**
	 * @param kSessionBase $ksObject
	 */
	public function setKsObject($ksObject) {
		$this->ksObject = $ksObject;
	}
}
