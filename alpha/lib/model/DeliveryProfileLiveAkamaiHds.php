<?php

class DeliveryProfileLiveAkamaiHds extends DeliveryProfileLiveHds {
	
	public function checkIsLive ($url)
	{
		$url = kDeliveryUtils::addQueryParameter($url, "hdcore=" . kConf::get('hd_core_version'));
		return parent::checkIsLive($url);
	}
}

