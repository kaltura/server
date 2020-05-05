<?php

class DeliveryProfileLiveHds extends DeliveryProfileLive {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function checkIsLive ($url)
	{
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

	protected function getHttpUrl($entryServerNode)
	{
		$baseUrl = $this->getBaseUrl($entryServerNode->serverNode);
		return rtrim($baseUrl, "/") . "/" . $this->getStreamName() . "/manifest.f4m" . $this->getQueryAttributes();
	}
	
	/**
	 * Fetch the manifest and build all flavors array
	 * @param string $url
	 */
	private function buildF4mFlavors($url, array &$flavors, array &$bootstrapInfos)
	{
		$manifest = KCurlWrapper::getContent($url);
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
//	public function doServe(kLiveStreamConfiguration $liveStreamConfig)	{
//		if($backupUrl)
//		{
//			$entryId = $this->params->getEntryId();
//			$entry = entryPeer::retrieveByPK($entryId);
//			
//			if($this->params->getResponseFormat() == 'f4m')
//			{
//				$flavors = array();
//				$bootstrapInfos = array();
//				$this->buildF4mFlavors($baseUrl, $flavors, $bootstrapInfos);
//				$this->buildF4mFlavors($backupUrl, $flavors, $bootstrapInfos);
//				
//				$renderer = $this->getRenderer($flavors);
//				if($renderer instanceof kF4MManifestRenderer)
//				{
//					$renderer->bootstrapInfos = $bootstrapInfos;
//					if($entry->getDvrStatus() == DVRStatus::ENABLED)
//					{
//						$renderer->streamType = kF4MManifestRenderer::PLAY_STREAM_TYPE_DVR;
//						$renderer->dvrWindow = $entry->getDvrWindow() ? $entry->getDvrWindow() : '7200';
//					}
//					$renderer->mimeType = 'video/mp4';
//				}
//				return $renderer;
//			}
//	
//			if($entry)
//			{
//		 		$protocol = $this->params->getMediaProtocol();
//		 		$baseUrl = $this->getUrl();
//			
//				$parameters = array_merge(requestUtils::getRequestParams(), array(
//					'protocol' => $protocol,
//					'format' => 'hds',
//					'responseFormat' => 'f4m'
//				));
//				$queryStringParameters = array();
//				foreach($parameters as $parameter => $value)
//				{
//					if(is_int(strpos($value, '/')))
//					{
//						$queryStringParameters[$parameter] = $value;
//						unset($parameters[$parameter]);
//					}
//				}
//				$requestParams = requestUtils::buildRequestParams($parameters);
//				
//		 		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());
//				$baseUrl .= "{$partnerPath}/playManifest/$requestParams/1/a.f4m";
//				
//				if(count($queryStringParameters))
//				{
//					$baseUrl .= '?' . http_build_query($queryStringParameters);
//				}
//			}
//		}
		
//		$flavors = array();
//		$baseUrl = $liveStreamConfig->getUrl();
//		$this->finalizeUrls($baseUrl, $flavors);
//		$flavors[] = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
//		$renderer = $this->getRenderer($flavors);
//		return $renderer;
//	}
}

