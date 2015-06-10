<?php
class DeliveryProfileLiveRtmfp extends DeliveryProfileLive
{
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function serve($baseUrl, $backupUrl) {
		$entry = entryPeer::retrieveByPK($this->params->getEntryId());
		if ($entry instanceof LiveEntry)
		{
			$pushPublishRtmfpConfiguration = $entry->getLiveStreamConfigurationByProtocol(PlaybackProtocol::RTMFP, 'rtmp');
			/* @var $pushPublishRtmfpConfiguration kLiveStreamRtmfpConfiguration */
			$renderer = $this->getRenderer(array());
			
			/* @var $renderer kF4MManifestRenderer */
			$renderer->groupspec = $pushPublishRtmfpConfiguration->getGroupspec();
			$renderer->mediaUrl = $pushPublishRtmfpConfiguration->getUrl();
			$renderer->multicastStreamName = $pushPublishRtmfpConfiguration->getMulticastStreamName();
			return $renderer;
		}
	}
}