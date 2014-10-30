<?php

class DeliveryProfileVodPackagerDash extends DeliveryProfileDash {
		
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		$url .= '/forceproxy/true';

		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url;
	}
	
	public function serve()
	{
		$flavor = $this->getVodPackagerUrl('/manifest.mpd');
		
		return $this->getRenderer(array($flavor));
	}
	
	protected static function generateMultiUrl(array $flavors)
	{
		$urls = array();
		foreach ($flavors as $flavor)
		{
			$urls[] = $flavor['url'];
		}
		$urls = array_unique($urls);
	
		if (count($urls) == 1)
		{
			$baseUrl = reset($urls);
			return '/' . ltrim($baseUrl, '/');
		}
	
		$prefix = kString::getCommonPrefix($urls);
		$prefixLen = strlen($prefix);
		$postfix = kString::getCommonPostfix($urls);
		$postfixLen = strlen($postfix);
		$middlePart = ',';
		foreach ($urls as $url)
		{
			$middlePart .= substr($url, $prefixLen, strlen($url) - $prefixLen - $postfixLen) . ',';
		}
		$baseUrl = $prefix . $middlePart . $postfix;
	
		return '/' . ltrim($baseUrl, '/') . '.urlset';
	}
	
	/**
	 * @return array
	 */
	protected function getVodPackagerUrl($urlSuffix)
	{
		$flavors = $this->buildHttpFlavorsArray();

		$url = self::generateMultiUrl($flavors);
		$url .= $urlSuffix;
		
		$urlPrefix = $this->getUrl();
		
		// move any folders on the url prefix to the url part, so that the protocol folder will always be first
		$urlPrefixWithProtocol = $urlPrefix;
		if (strpos($urlPrefix, '://') === false)
			$urlPrefixWithProtocol = 'http://' . $urlPrefix;
		
		$urlPrefixPath = parse_url($urlPrefixWithProtocol, PHP_URL_PATH);
		if ($urlPrefixPath && substr($urlPrefix, -strlen($urlPrefixPath)) == $urlPrefixPath)
		{
			$urlPrefix = substr($urlPrefix, 0, -strlen($urlPrefixPath));
			$url = rtrim($urlPrefixPath, '/') . '/' . ltrim($url, '/');
		}
		
		if (strpos($urlPrefix, '://') === false)
			$urlPrefix = $this->params->getMediaProtocol() . '://' . $urlPrefix;
		
		return array('url' => $url, 'urlPrefix' => $urlPrefix);		
	}
}
