<?php
/**
 * @package plugins.chargeBee
 */

class kChargeBeeUtils
{
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'chargeBee';
	const COUNTRY_CODE_EUROPE = 'countryCodeEurope';
	const SITE_US = 'siteUS';
	const SITE_API_KEY_US = 'siteApiKeyUS';
	const SITE_EU = 'siteEU';
	const SITE_API_KEY_EU = 'siteApiKeyEU';
	const PLAN_ID = 'planId';
	const AUTO_COLLECTION = 'autoCollection';

	public static function getSiteConfig($country)
	{
		$chargeBeeConfMap = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME, array());
		if (!$chargeBeeConfMap)
		{
			KalturaLog::log('Could not find the map: ' . self::MAP_NAME . ' param name: ' . self::CONFIGURATION_PARAM_NAME);
			return array($chargeBeeConfMap, null, null);
		}
		$countryCodeEurope = array_map('trim', explode(',', $chargeBeeConfMap[self::COUNTRY_CODE_EUROPE]));
		$isEuropeCountry = array_key_exists($country, $countryCodeEurope);
		if ($isEuropeCountry)
		{
			$site = self::SITE_EU;
			$siteApiKey = self::SITE_API_KEY_EU;
		}
		else
		{
			$site = self::SITE_US;
			$siteApiKey = self::SITE_API_KEY_US;
		}
		if (!isset($chargeBeeConfMap[$site])|| !isset($chargeBeeConfMap[$siteApiKey]) || !isset($chargeBeeConfMap[self::PLAN_ID]) || !isset($chargeBeeConfMap[self::AUTO_COLLECTION]))
		{
			KalturaLog::log('Could not find the map: ' . self::MAP_NAME . ' params');
		}
		return array($chargeBeeConfMap, $site, $siteApiKey);
	}

	public static function getChargeBeeClient($country)
	{
		list($chargeBeeConfMap, $site, $siteApiKey) = self::getSiteConfig($country);
		if (!$chargeBeeConfMap || !$site || !$siteApiKey)
		{
			KalturaLog::log('Could not find the map: ' . self::MAP_NAME . ' params');
			return null;
		}
		return new kChargeBeeClient($chargeBeeConfMap[$site], $chargeBeeConfMap[$siteApiKey]);
	}

	public static function orderVendorIntegration($vendorIntegrationList)
	{
		$vendorIntegrationOrdered = array();
		foreach ($vendorIntegrationList as $vendorIntegrationItem)
		{
			$createdAt = gmdate("Y-m-d", $vendorIntegrationItem->createdAt);
			if(!isset($vendorIntegrationOrdered[$createdAt]))
			{
				$vendorIntegrationOrdered[$createdAt] = array();
			}
			$vendorIntegrationOrdered[$createdAt][] = $vendorIntegrationItem;
		}
		KalturaLog::debug('vendorIntegrationOrdered are' . print_r($vendorIntegrationOrdered, true));
		return $vendorIntegrationOrdered;
	}
}