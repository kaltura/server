<?php
class ContentDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaVersion, IKalturaSearchDataContributor, IKalturaObjectLoader, IKalturaAdminConsolePages, IKalturaAdminConsoleEntryInvestigate, IKalturaPending
{
	const PLUGIN_NAME = 'contentDistribution';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const CONTENT_DSTRIBUTION_MANAGER = 'kContentDistributionFlowManager';

	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		if($interface == 'IKalturaMrssContributor')
			return kContentDistributionMrssManager::get();
			
		return null;
	}
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function dependsOn()
	{
		$dependency = new KalturaDependency(MetadataPlugin::getPluginName());
		return array($dependency);
	}
	
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
			'distributionProfile' => 'DistributionProfileService',
			'entryDistribution' => 'EntryDistributionService',
			'distributionProvider' => 'DistributionProviderService',
			'genericDistributionProvider' => 'GenericDistributionProviderService',
			'genericDistributionProviderAction' => 'GenericDistributionProviderActionService',
			'contentDistributionBatch' => 'ContentDistributionBatchService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/content_distribution.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::CONTENT_DSTRIBUTION_MANAGER,
		);
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'BatchJobType')
			return array('ContentDistributionBatchJobType');
			
		return array();
	}
	
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
		if($object instanceof entry)
			return kContentDistributionManager::getEntrySearchValues($object);
			
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
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
	
		if($baseClass == 'ISyncableFile' && isset($constructorArgs['objectId']))
		{
			$objectId = $constructorArgs['objectId'];

			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION))
			{
				GenericDistributionProviderActionPeer::setUseCriteriaFilter(false);
				$object = GenericDistributionProviderActionPeer::retrieveByPK($objectId);
				GenericDistributionProviderActionPeer::setUseCriteriaFilter(true);
				return $object;
			}

			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::ENTRY_DISTRIBUTION))
			{
				EntryDistributionPeer::setUseCriteriaFilter(false);
				$object = EntryDistributionPeer::retrieveByPK($objectId);
				EntryDistributionPeer::setUseCriteriaFilter(true);
				return $object;
			}

			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::DISTRIBUTION_PROFILE))
			{
				DistributionProfilePeer::setUseCriteriaFilter(false);
				$object = DistributionProfilePeer::retrieveByPK($objectId);
				DistributionProfilePeer::setUseCriteriaFilter(true);
				return $object;
			}
		}
		
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return new kDistributionSubmitJobData();
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return new kDistributionUpdateJobData();
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return new kDistributionDeleteJobData();
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
				return new kDistributionFetchReportJobData();
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return new KalturaDistributionSubmitJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return new KalturaDistributionUpdateJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return new KalturaDistributionDeleteJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
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
			
		if($baseClass == 'ISyncableFile')
		{
			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION))
				return 'GenericDistributionProviderAction';
			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::ENTRY_DISTRIBUTION))
				return 'EntryDistribution';
			if($enumValue == self::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::DISTRIBUTION_PROFILE))
				return 'DistributionProfile';
		}
		
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return 'kDistributionSubmitJobData';
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return 'kDistributionUpdateJobData';
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return 'kDistributionDeleteJobData';
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
				return 'kDistributionFetchReportJobData';
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return 'KalturaDistributionSubmitJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return 'KalturaDistributionUpdateJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return 'KalturaDistributionDeleteJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
				return 'KalturaDistributionFetchReportJobData';
		}
		
		return null;
	}
	
	public static function getAdminConsolePages()
	{
		$pages = array();
		
		$pages[] = new GenericDistributionProvidersListAction();
		$pages[] = new GenericDistributionProviderConfigureAction();
		$pages[] = new GenericDistributionProviderDeleteAction();
		
		$pages[] = new DistributionProfileListAction();
		$pages[] = new DistributionProfileConfigureAction();
		$pages[] = new DistributionProfileUpdateStatusAction();

		return $pages;
	}
	
	/**
	 * @return array<Kaltura_View_Helper_EntryInvestigatePlugin>
	 */
	public static function getEntryInvestigatePlugins()
	{
		return array(
			new Kaltura_View_Helper_EntryInvestigateDistribution(),
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
}
