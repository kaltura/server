<?php

class DeliveryProfileLivePackagerHls extends DeliveryProfileLiveAppleHttp {
	
	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getLivePackagerUrl($serverNode, PlaybackProtocol::HLS);
		$httpUrl .= "master.m3u8";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
	
	/**
	 * Build all streaming flavors array
	 * @param string $url
	 */
	protected function buildM3u8Flavors($url, array &$flavors, array $kLiveStreamParamsArray, $flavorBitrateInfo = array())
	{
		$domainPrefix = $this->getDeliveryServerNodeUrl(true);
		
		foreach ($kLiveStreamParamsArray as $kLiveStreamParams)
		{
			/* @var $kLiveStreamParams kLiveStreamParams */
			/* @var $stream kLiveStreamParams */
			$flavor = array(
				'url' => '',
				'urlPrefix' => requestUtils::resolve("index-s" . $kLiveStreamParams->getFlavorId() . ".m3u8" , $url),
				'domainPrefix' => $domainPrefix,
				'ext' => 'm3u8',
			);
			
			$flavor['bitrate'] = isset($flavorBitrateInfo[$kLiveStreamParams->getFlavorId()]) ? $flavorBitrateInfo[$kLiveStreamParams->getFlavorId()] : $kLiveStreamParams->getBitrate();
			$flavor['bitrate'] = $flavor['bitrate'] / 1024;
			$flavor['width'] = $kLiveStreamParams->getWidth();
			$flavor['height'] = $kLiveStreamParams->getHeight();
			
			$this->addLanguageInfo($flavor, $kLiveStreamParams);
			
			$flavors[] = $flavor;
		}
	}
}
