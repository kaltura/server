<?php

class DeliveryProfileLivePackagerHls extends DeliveryProfileLiveAppleHttp {
	
	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getLivePackagerUrl($serverNode, PlaybackProtocol::HLS);
		$httpUrl .= "master.m3u8";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
	
	protected function getUrlPrefix($url, $kLiveStreamParams)
	{
		return requestUtils::resolve("index-s" . $kLiveStreamParams->getFlavorId() . ".m3u8" , $url);
	}
}
