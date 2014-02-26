<?php

class DeliveryProfileLiveAkamaiHds extends DeliveryProfileLiveHds {
	
	public function isLive ($url)
	{
		$parsedUrl = parse_url($url);
		if (isset($parsedUrl['query']) && strlen($parsedUrl['query']) > 0)
			$url .= '&hdcore='.kConf::get('hd_core_version');
		else
			$url .= '?hdcore='.kConf::get('hd_core_version');
		
		return $this->doCheckIsLive($url);
	}
}

