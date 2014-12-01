<?php

class VodPackagerDeliveryUtils
{
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
	
	public static function getVodPackagerUrl($flavors, $urlPrefix, $urlSuffix, DeliveryProfileDynamicAttributes $params)
	{
		$url = self::generateMultiUrl($flavors);
		$url .= $urlSuffix;
	
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
			$urlPrefix = $params->getMediaProtocol() . '://' . $urlPrefix;
	
		return array('url' => $url, 'urlPrefix' => $urlPrefix);
	}
}
