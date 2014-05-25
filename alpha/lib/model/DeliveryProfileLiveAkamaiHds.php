<?php

class DeliveryProfileLiveAkamaiHds extends DeliveryProfileLiveHds {
	
	public function isLive ($url)
	{
		$url = kDeliveryUtils::addQueryParameter($url, kConf::get('hd_core_version'));
		return parent::isLive($url);
	}
}

