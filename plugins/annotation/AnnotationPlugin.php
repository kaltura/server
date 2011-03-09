<?php
/**
 * @package plugins.annotation
 */
class AnnotationPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEventConsumers, IKalturaMemoryCleaner
{
	const PLUGIN_NAME = 'annotation';
	const ANNOTATION_MANAGER = 'kAnnotationManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}

	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'annotation' => 'AnnotationService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/annotation.ct');
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::ANNOTATION_MANAGER,
		);
	}

	public static function cleanMemory()
	{
	    AnnotationPeer::clearInstancePool();
	}
}
