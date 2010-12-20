<?php
class VirusScanPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'virusScan';
	const VIRUS_SCAN_FLOW_MANAGER_CLASS = 'kVirusScanFlowManager';
	
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
			'virusScanProfile' => 'VirusScanProfileService',
			'virusScanBatch' => 'VirusScanBatchService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/virus_scan.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::VIRUS_SCAN_FLOW_MANAGER_CLASS
		);
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName)
	{
		// virus scan only works in api_v3 context because it uses dynamic enums
		if (!kCurrentContext::isApiV3BootstrapLoaded())
			return array();
			
		if($baseEnumName == 'entryStatus')
			return array('VirusScanEntryStatus');
			
		if($baseEnumName == 'BatchJobType')
			return array('VirusScanBatchJobType');
			
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		// virus scan only works in api_v3 context because it uses dynamic enums
		if (!kCurrentContext::isApiV3BootstrapLoaded())
			return null;
			
		if($baseClass == 'kJobData')
		{
			if($enumValue == VirusScanBatchJobType::get()->coreValue(VirusScanBatchJobType::VIRUS_SCAN))
			{
				return new kVirusScanJobData();
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == VirusScanBatchJobType::get()->apiValue(VirusScanBatchJobType::VIRUS_SCAN))
			{
				return new KalturaVirusScanJobData();
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		// virus scan only works in api_v3 context because it uses dynamic enums
		if (!kCurrentContext::isApiV3BootstrapLoaded())
			return null;
			
		if($baseClass == 'kJobData')
		{
			if($enumValue == VirusScanBatchJobType::get()->coreValue(VirusScanBatchJobType::VIRUS_SCAN))
			{
				return 'kVirusScanJobData';
			}
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == VirusScanBatchJobType::get()->apiValue(VirusScanBatchJobType::VIRUS_SCAN))
			{
				return 'KalturaVirusScanJobData';
			}
		}
		
		return null;
	}

}
