<?php
/**
 * @package plugins.partnerAggregation
 */
class PartnerAggregationPlugin extends KalturaPlugin implements IKalturaServices
{
	const PLUGIN_NAME = 'partnerAggregation';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'partnerAggregation' => 'PartnerAggregationService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/partner_aggregation.ct');
	}
}
