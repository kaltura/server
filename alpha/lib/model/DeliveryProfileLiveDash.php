<?php

class DeliveryProfileLiveDash extends DeliveryProfileLive
{
	function __construct()
	{
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	public function checkIsLive($url)
	{
		$content = $this->urlExists($url, array('application/dash+xml'));
		if(!$content)
		{
			return false;
		}
		
		return $this->doesMinimumUpdatePeriodExist($content);
	}

	protected function getHttpUrl($entryServerNode)
	{
		$baseUrl = $this->getBaseUrl($entryServerNode->serverNode);
		return rtrim($baseUrl, "/") . "/" . $this->getStreamName() . "/manifest.mpd" . $this->getQueryAttributes();
	}
	
	/**
	 * @param string $content
	 * @return bool
	 */
	protected function doesMinimumUpdatePeriodExist($content)
	{
		try
		{
			$xml = new SimpleXMLElement($content);
		}
		catch (Exception $e)
		{
			KalturaLog::debug('Failed to parse MPD content as XML: ' . $e->getMessage());
			return false;
		}

		$minimumUpdatePeriod = $xml->attributes()->minimumUpdatePeriod;
		return $minimumUpdatePeriod ? true : false;
	}
}

