<?php
class AuditPlugin implements KalturaPlugin, KalturaServicesPlugin, KalturaEventConsumersPlugin
{
	const PLUGIN_NAME = 'audit';
	const AUDIT_TRAIL_MANAGER = 'kAuditTrailManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'auditTrail' => 'AuditTrailService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/audit.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::AUDIT_TRAIL_MANAGER,
		);
	}
}
