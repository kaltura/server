<?php

class DeliveryProfileLiveAkamaiHds extends DeliveryProfileLiveHds {
	
	protected function initLiveStreamConfig()
	{
		$this->liveStreamConfig = new kLiveStreamConfiguration();
		
		$entry = $this->getDynamicAttributes()->getEntry();
		if(in_array($entry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE)))
		{
			$customLiveStreamConfigurations = $entry->getCustomLiveStreamConfigurations();
			foreach($customLiveStreamConfigurations as $customLiveStreamConfiguration)
			{
				/* @var $customLiveStreamConfiguration kLiveStreamConfiguration */
				if($this->getDynamicAttributes()->getFormat() == $customLiveStreamConfiguration->getProtocol()) 
				{
					$this->liveStreamConfig = $customLiveStreamConfiguration;
					return;
				}
			}
		}
		
		KalturaLog::debug("Could not locate custom live stream configuration from entry");
		return parent::initLiveStreamConfig();
	}
	
	public function checkIsLive ($url)
	{
		$url = kDeliveryUtils::addQueryParameter($url, "hdcore=" . kConf::get('hd_core_version'));
		return parent::checkIsLive($url);
	}
}

