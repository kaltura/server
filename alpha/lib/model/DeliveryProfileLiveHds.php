<?php

class DeliveryProfileLiveHds extends DeliveryProfileLive {
	
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	protected function doCheckIsLive($url) {
		$data = $this->urlExists($url, array('video/f4m'));
		if (is_bool($data))
			return $data;
		
		$element = new KDOMDocument();
		$element->loadXML($data);
		$streamType = $element->getElementsByTagName('streamType')->item(0);
		if ($streamType->nodeValue == 'live')
			return true;
		
		return false;
	}
	
	public function isLive ($url)
	{
		return $this->doCheckIsLive($url);
	}
	
	/**
	 * Fetch the manifest and build all flavors array
	 * @param string $url
	 */
	private function buildF4mFlavors($url, array &$flavors, array &$bootstrapInfos)
	{
		$manifest = requestUtils::getContent($url);
		if(!$manifest)
			return;
	
		$manifest = preg_replace('/xmlns="[^"]+"/', '', $manifest);
		$xml = new SimpleXMLElement($manifest);
		$mediaElements = $xml->xpath('/manifest/media');
		
		foreach($mediaElements as $mediaElement)
		{
			/* @var $mediaElement SimpleXMLElement */
			$flavor = array('urlPrefix' => '');
			$playlistUrl = null;
			foreach($mediaElement->attributes() as $attr => $attrValue)
			{
				$attrValue = "$attrValue";
				
				if($attr === 'url')
					$attrValue = requestUtils::resolve($attrValue, $url);
					
				if($attr === 'bootstrapInfoId')
				{
					$bootstrapInfoElements = $xml->xpath("/manifest/bootstrapInfo[@id='$attrValue']");
					if(count($bootstrapInfoElements))
					{
						$bootstrapInfoElement = reset($bootstrapInfoElements);
						/* @var $bootstrapInfoElement SimpleXMLElement */
						$playlistUrl = requestUtils::resolve(strval($bootstrapInfoElement['url']), $url);
					}
				}
					
				$flavor["$attr"] = $attrValue;
			}
			
			if($playlistUrl)
			{
				$playlistId = md5($playlistUrl);
				$bootstrapInfo = array(
					'id' => $playlistId,
					'url' => $playlistUrl,
				);
				$bootstrapInfos[$playlistId] = $bootstrapInfo;
				
				$flavor['bootstrapInfoId'] = $playlistId;
			}
			
			$flavors[] = $flavor;
		}
	}
	
	/* (non-PHPdoc)
	 * @see DeliveryProfileLive::serve()
	 */
	public function serve($baseUrl, $backupUrl) 
	{
		if($backupUrl)
		{
			$entryId = $this->params->getEntryId();
			$entry = entryPeer::retrieveByPK($entryId);
			
			if($this->params->getResponseFormat() == 'f4m')
			{
				$flavors = array();
				$bootstrapInfos = array();
				$this->buildF4mFlavors($baseUrl, $flavors, $bootstrapInfos);
				$this->buildF4mFlavors($backupUrl, $flavors, $bootstrapInfos);
				
				$renderer = $this->getRenderer($flavors);
				if($renderer instanceof kF4MManifestRenderer)
				{
					$renderer->bootstrapInfos = $bootstrapInfos;
					if($entry && $entry instanceof LiveEntry)
					{
						if($entry->getDvrStatus() == DVRStatus::ENABLED)
						{
							$renderer->streamType = kF4MManifestRenderer::PLAY_STREAM_TYPE_DVR;
							$renderer->dvrWindow = $entry->getDvrWindow() ? $entry->getDvrWindow() : '7200';
						}
						$renderer->mimeType = 'video/mp4';
					}
				}
				return $renderer;
			}
	
			if($entry)
			{
				$this->params->setResponseFormat('f4m');
		 		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());
		 		
		 		$protocol = $this->params->getMediaProtocol();
		 		$hostName = myPartnerUtils::getCdnHost($entry->getPartnerId(), $protocol);
		 		$baseUrl = "$protocol://$hostName/$partnerPath/playManifest/entryId/$entryId/protocol/$protocol/format/hds/responseFormat/f4m/a.f4m";
			}
		}
		
		$flavor = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
		$renderer = $this->getRenderer(array($flavor));
		return $renderer;
	}
}

