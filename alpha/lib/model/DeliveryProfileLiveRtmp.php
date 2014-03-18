<?php

class DeliveryProfileLiveRtmp extends DeliveryProfileLive {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	public function setEnforceRtmpe($v)
	{
		$this->putInCustomData("enforceRtmpe", $v);
	}
	
	public function getEnforceRtmpe()
	{
		return $this->getFromCustomData("enforceRtmpe");
	}
	
	public function serve($baseUrl) {
		$flavors = $this->buildRtmpLiveStreamFlavorsArray();
		
		$this->finalizeUrls($baseUrl, $flavors);
		
		$renderer = $this->getRenderer($flavors);
		$renderer->baseUrl = $baseUrl;
		$renderer->streamType = kF4MManifestRenderer::PLAY_STREAM_TYPE_LIVE;
		return $renderer;
	}
	
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if ($this->getEnforceRtmpe())
		{
			$baseUrl = preg_replace('/^rtmp:\/\//', 'rtmpe://', $baseUrl);
			$baseUrl = preg_replace('/^rtmpt:\/\//', 'rtmpte://', $baseUrl);
		}
		parent::finalizeUrls($baseUrl, $flavorsUrls);
	}
	
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	protected function buildRtmpLiveStreamFlavorsArray()
	{
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		if ($entry->getSource() == EntrySourceType::LIVE_STREAM || $entry->getSource() == EntrySourceType::LIVE_CHANNEL)
		{
			$flavors = array(
					0 => array(
							'url' => $entry->getStreamName(),
							'bitrate' => 0,
							'width' => 0,
							'height' => 0,
					)
			);
				
			$conversionProfileId = $entry->getConversionProfileId();
			if($conversionProfileId)
			{
				$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
				$liveParams = $this->params->filterFlavorsByTags($liveParams);
	
				if(count($liveParams))
				{
					$flavors = array();
					foreach($liveParams as $index => $liveParamsItem)
					{
						/* @var $liveParamsItem liveParams */
						$flavors[$index] = array(
								'url' => $entry->getStreamName() . '_' . $liveParamsItem->getId(),
								'bitrate' => $liveParamsItem->getVideoBitrate(),
								'width' => $liveParamsItem->getWidth(),
								'height' => $liveParamsItem->getHeight(),
						);
					}
				}
			}
				
			return $flavors;
		}
			
		$flavors = $entry->getStreamBitrates();
		if(count($flavors))
		{
			foreach($flavors as $index => $flavor)
			{
				$brIndex = $index + 1;
				$flavors[$index] = $flavor;
				$flavors[$index]['url'] = str_replace('%i', $brIndex, $entry->getStreamName());
			}
		}
		else
		{
			$flavors[0] = array(
					'url' => str_replace('%i', '1', $entry->getStreamName()),
					'bitrate' => 0,
					'width' => 0,
					'height' => 0,
			);
		}
	
		return $flavors;
	}
}

