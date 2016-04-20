<?php

class DeliveryProfileLiveSilverLight extends DeliveryProfileLive {

	public function setLiveStreamConfig(kLiveStreamConfiguration $liveStreamConfig)
	{
		$this->liveStreamConfig = $liveStreamConfig;
	}

}

