<?php

class kUrlTokenizerPlaybackContext extends kUrlTokenizer
{
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$playbackContext = $this->getPlaybackContext();
		if(!$playbackContext)
			return $url;
		
		$playbackContext = http_build_query(array("playbackContext" => $this->getPlaybackContext()));
		$url = $this->appendPlaybackContext($url, $playbackContext);
		return $url;
	}
	
	protected function appendPlaybackContext($url, $playbackContext)
	{
		if (strpos($url, '?') === false)
			$url .= '?';
		else
			$url .= '&';
		return $url . $playbackContext;
	}
}
