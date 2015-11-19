<?php

class DeliveryProfileLiveRtmp extends DeliveryProfileLive {
	
	protected $baseUrl = '';
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function setEnforceRtmpe($v)
	{
		$this->putInCustomData("enforceRtmpe", $v);
	}
	
	public function getEnforceRtmpe()
	{
		return $this->getFromCustomData("enforceRtmpe");
	}
	
	public function buildServeFlavors()
	{
		$flavors = $this->buildRtmpLiveStreamFlavorsArray();

		$baseUrl = $this->liveStreamConfig->getUrl();
		$this->finalizeUrls($baseUrl, $flavors);
		$this->baseUrl = $baseUrl;
		
		return $flavors;
	}
	
	public function getRenderer($flavors)
	{
		$renderer = parent::getRenderer($flavors);
		$renderer->baseUrl = $this->baseUrl;
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
		if (in_array($entry->getSource(), LiveEntry::$kalturaLiveSourceTypes)) 
		{
			/* @var $entry LiveEntry */
			$flavors = array( 0 => $this->getFlavorAssetInfo($entry->getStreamName()) );
				
			$conversionProfileId = $entry->getConversionProfileId();
			if($conversionProfileId)
			{
				$liveParams = assetParamsPeer::retrieveByProfile($conversionProfileId);
	
				if(count($liveParams))
				{
					$flavors = array();
					foreach($liveParams as $index => $liveParamsItem)
					{
						/* @var $liveParamsItem liveParams */
						if($entry->getLiveStreamConfigurationByProtocol(PlaybackProtocol::RTMP, 'rtmp'))
						{
							$configuration = $entry->getLiveStreamConfigurationByProtocol(PlaybackProtocol::RTMP, 'rtmp');
							$flavors[$index] = $this->getFlavorAssetInfo(str_replace("%i", $liveParamsItem->getId(), $configuration->getStreamName()), '', $liveParamsItem);		
							continue;
						}
						$flavors[$index] = $this->getFlavorAssetInfo($entry->getStreamName() . '_' . $liveParamsItem->getId(), '', $liveParamsItem);
  					}
				}
			}
				
			return $flavors;
		}
			
		$tmpFlavors  = $entry->getStreamBitrates();
 		if(count($tmpFlavors))
		{
			$flavors = array();
 			foreach($tmpFlavors as $index => $flavor)
			{
				$brIndex = $index + 1;
				$flavors[$index] = $this->getFlavorAssetInfo(str_replace('%i', $brIndex, $entry->getStreamName()));
 				$flavors[$index] = array_merge($flavors[$index], $flavor);
			}
		}
		else
		{
			$flavors[0] = $this->getFlavorAssetInfo(str_replace('%i', '1', $entry->getStreamName()));
		}
	
		return $flavors;
	}
}

