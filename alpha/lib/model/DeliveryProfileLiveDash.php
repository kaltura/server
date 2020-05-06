<?php

class DeliveryProfileLiveDash extends DeliveryProfileLive
{
	function __construct()
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kDashRedirectManifestRenderer';
	}
	
	public function checkIsLive($url)
	{
		$content = $this->urlExists($url, array('application/dash+xml'));
		if(!$content)
		{
			return false;
		}
		
		$mediaUrls = $this->getDashUrls($content);
		foreach($mediaUrls as $mediaUrl)
		{
			$mediaUrl = requestUtils::resolve($mediaUrl, $url);
			if($this->urlExists($mediaUrl, array('audio/mp4', 'video/mp4'), '0-1') !== false)
			{
				return true;
			}
		}
		
		return false;
	}

	protected function getHttpUrl($entryServerNode)
	{
		$baseUrl = $this->getBaseUrl($entryServerNode->serverNode);
		return rtrim($baseUrl, "/") . "/" . $this->getStreamName() . "/manifest.mpd" . $this->getQueryAttributes();
	}
	
	/**
	 * @param string $content
	 * @return array
	 */
	protected function getDashUrls($content)
	{
		$xml = new SimpleXMLElement($content);
		$mediaItems = $xml->xpath('//*[@media]');
		if(!count($mediaItems))
			return array();
		
		$mediaUrls = array();
		foreach($mediaItems as $mediaItem)
		{
			/* @var $mediaItem SimpleXMLElement */
			$mediaUrls[] = strval($mediaItem->attributes()->media);
		}
		
		return $mediaUrls;
	}
}

