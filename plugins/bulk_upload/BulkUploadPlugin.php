<?php
/**
 * @package plugins.bulkUpload
 */
class BulkUploadPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaVersion, IKalturaSearchDataContributor, IKalturaObjectLoader, IKalturaAdminConsolePages, IKalturaAdminConsoleEntryInvestigate, IKalturaPending, IKalturaMemoryCleaner
{
	const PLUGIN_NAME = 'bulkUpload';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
//	const CONTENT_DSTRIBUTION_MANAGER = 'kContentDistributionFlowManager';

	/**
	 * (non-PHPdoc)
	 * @see KalturaPlugin::getInstance()
	 */
	public function getInstance($interface)
	{
		return null;
		
//		if($this instanceof $interface)
//			return $this;
//			
//		if($interface == 'IKalturaMrssContributor')
//			return kContentDistributionMrssManager::get();
//			
//		return null;
	}
	
	/**
	 * 
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * 
	 * Returns the plugin dependency
	 */
	public static function dependsOn()
	{
		return array();
//		$dependency = new KalturaDependency(MetadataPlugin::getPluginName());
//		return array($dependency);
	}
	
	/**
	 * 
	 * Returns if the plugin is enable for the partner
	 * @param int $partnerId
	 */
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
			
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
//			'distributionProfile' => 'DistributionProfileService',
//			'entryDistribution' => 'EntryDistributionService',
//			'distributionProvider' => 'DistributionProviderService',
//			'genericDistributionProvider' => 'GenericDistributionProviderService',
//			'genericDistributionProviderAction' => 'GenericDistributionProviderActionService',
//			'contentDistributionBatch' => 'ContentDistributionBatchService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		//TOOD: Roni - ask TanTan if i need to create for permissions
		return realpath(dirname(__FILE__).'/config/content_distribution.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		//TOOD: Roni - ask TanTan if i need to create for events
		return array(
//			self::CONTENT_DSTRIBUTION_MANAGER,
		);
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		print("In My Get Enums!!!\n");
		if(is_null($baseEnumName))
			return array('BulkUploadBatchJobType');
	
		if($baseEnumName == 'BatchJobType')
			return array('BulkUploadBatchJobType');
		
		return array();
	}
	
	/**
	 * 
	 * Returns the plugin version
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}
	
	/**
	 * Return textual search data to be associated with the object
	 * 
	 * @param BaseObject $object
	 * @return string
	 */
	public static function getSearchData(BaseObject $object)
	{
//		if($object instanceof entry)
//			return kContentDistributionManager::getEntrySearchValues($object);
			
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		// bulk upload does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
		
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBatchJobTypeCoreValue(BulkUploadBatchJobType::CSV))
				return new kDistributionSubmitJobData();
				
			if($enumValue == self::getBatchJobTypeCoreValue(BulkUploadBatchJobType::XML))
				return new kDistributionUpdateJobData();
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return new KalturaDistributionSubmitJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return new KalturaDistributionUpdateJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return new KalturaDistributionDeleteJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
				return new KalturaDistributionFetchReportJobData();
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
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
			
//		if($baseClass == 'kJobData')
//		{
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
//				return 'kDistributionSubmitJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
//				return 'kDistributionUpdateJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
//				return 'kDistributionDeleteJobData';
//				
//			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
//				return 'kDistributionFetchReportJobData';
//		}
//	
//		if($baseClass == 'KalturaJobData')
//		{
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
//				return 'KalturaDistributionSubmitJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
//				return 'KalturaDistributionUpdateJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
//				return 'KalturaDistributionDeleteJobData';
//				
//			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
//				return 'KalturaDistributionFetchReportJobData';
//		}
		
		return null;
	}
	
	/**
	 * 
	 * Returns the admin console pages needed for bulk upload
	 */
	public static function getAdminConsolePages()
	{
		$pages = array();
		
//		$pages[] = new DistributionProfileListAction();
//		$pages[] = new DistributionProfileConfigureAction();
//		$pages[] = new DistributionProfileUpdateStatusAction();
//		
//		$pages[] = new GenericDistributionProvidersListAction();
//		$pages[] = new GenericDistributionProviderConfigureAction();
//		$pages[] = new GenericDistributionProviderDeleteAction();
//		
		return $pages;
	}
	
	/**
	 * @return array<Kaltura_View_Helper_EntryInvestigatePlugin>
	 */
	public static function getEntryInvestigatePlugins()
	{
		return array(
//			new Kaltura_View_Helper_EntryInvestigateDistribution(),
		);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getContentDistributionFileSyncObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('FileSyncObjectType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	/**
	 * 
	 * Cleans used memory
	 */
	public static function cleanMemory()
	{
		//TODO: Roni - add clean memory
	    DistributionProfilePeer::clearInstancePool();
	    EntryDistributionPeer::clearInstancePool();
	}
}
