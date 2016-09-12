<?php

class DeliveryProfileLivePackagerDash extends DeliveryProfileLiveDash
{
	protected function getHttpUrl($serverNode)
	{
		$httpUrl = $this->getLivePackagerUrl($serverNode, PlaybackProtocol::HLS);
		$httpUrl .= "manifest.mpd";
		
		KalturaLog::debug("Live Stream url [$httpUrl]");
		return $httpUrl;
	}
}

