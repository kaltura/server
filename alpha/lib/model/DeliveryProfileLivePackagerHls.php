<?php

class DeliveryProfileLivePackagerHls extends DeliveryProfileLiveAppleHttp {
	
	protected function getHttpUrl($entryServerNode)
	{
		$httpUrl = $this->getLivePackagerUrl($entryServerNode, PlaybackProtocol::HLS);
		
		$httpUrl .= "master";
		
		foreach($this->getDynamicAttributes()->getFlavorParamIds() as $flavorId)
		{
			$httpUrl .= "-s$flavorId";
		}
		
		$httpUrl .= ".m3u8";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
	
	protected function getUrlPrefix($url, $kLiveStreamParams)
	{
		return requestUtils::resolve("index-s" . $kLiveStreamParams->getFlavorId() . ".m3u8" , $url);
	}
}
