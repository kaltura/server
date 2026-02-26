<?php
/**
 * @package Admin
 * @subpackage Utils
 */
class Kaltura_PartnerUtils
{
	public static function getRegionalCdnHost($requestedUrl = null)
	{
		$protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

		$headerMapping = Zend_Registry::get('config')->settings->regionalCdnHeaderMapping->toArray();
		foreach ($headerMapping as $headerKey => $suffix)
		{
			if (!empty($_SERVER[$headerKey]))
			{
				return self::applyCdnSuffix($requestedUrl, $suffix, $protocol);
			}
		}

		return $requestedUrl;
	}

	private static function applyCdnSuffix($url, $suffix, $protocol)
	{
		$parsedUrl = parse_url($url);
		$parsedUrl['host'] = $parsedUrl['host'] . '.' . $suffix;
		return self::buildUrlFromParts($protocol, $parsedUrl);
	}

	private static function buildUrlFromParts($protocol, $parsedUrl)
	{
		$url = $protocol . '://' . $parsedUrl['host'];
		$url .= isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
		$url .= $parsedUrl['path'] ?? '';
		$url .= isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
		$url .= isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
		return $url;
	}
}
