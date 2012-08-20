<?php

class kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $playbackContext;
	
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
	 * @return string
	 */
	public function tokenizeSingleUrl($url)
	{
		return $url;
	}
	
	/**
	 * @param string $baseUrl
	 * @param array $flavors
	 */
	public function tokenizeMultiUrls(&$baseUrl, &$flavors)
	{
	}
}
