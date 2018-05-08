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
		if($this->getPlaybackContext())
			$url = kDeliveryUtils::addQueryParameter($url, "playbackContext=".$this->getPlaybackContext());
		return $url;
	}
}
