<?php
class KalturaServiceConfig extends myServiceConfig
{
	public function __construct($file_name , $service_name = null)
	{
	    KalturaServiceConfig::setStrictMode(false);
	    
		$serviceConfigs = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaServices');
		foreach($pluginInstances as $pluginInstance)
			$serviceConfigs[] = $pluginInstance->getServiceConfig();
		
	    foreach($serviceConfigs as $serviceConfig)
	    	KalturaServiceConfig::addSecondaryConfigTables($serviceConfig);
	    
		parent::myServiceConfig($file_name , $service_name);
	} 
	
	public static function getServiceConfigForPartner ( $partner )
	{
		kConfigTable::$should_use_cache = false;
		
		if ( $partner )
		{
			if($partner->getStatus() == Partner::PARTNER_STATUS_CONTENT_BLOCK)
			{
				$service_config_id = Partner::CONTENT_BLOCK_SERVICE_CONFIG_ID;
			}
			elseif($partner->getStatus() == Partner::PARTNER_STATUS_FULL_BLOCK)
			{
				$service_config_id = Partner::FULL_BLOCK_SERVICE_CONFIG_ID;
			}
			else
			{
				$service_config_id = $partner->getServiceConfigId() ;
			}
		}
		else 
			$service_config_id = null;
		$file_name = $service_config_id ? "v3_" . $service_config_id  : null;
		return  self::getInstance (  $file_name  );	
	}
	
	
	public static function getInstance ( $file_name , $service_name = null )
	{
		// TODO - maybe cache ??
		return new KalturaServiceConfig ( $file_name , $service_name  );
	}
	
	
	protected function getDefaultName ()
	{
		return "v3_services.ct";
	}
	
	protected  function getPath ()
	{
		return KALTURA_API_PATH.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR;
	}
}
?>