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
	
	public function serve($baseUrl) {
		$flavor = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
		$renderer = $this->getRenderer(array($flavor));
		return $renderer;
	}
}

