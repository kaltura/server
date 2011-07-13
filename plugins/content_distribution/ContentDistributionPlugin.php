<?php
/**
 * @package plugins.contentDistribution
 */
class ContentDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaVersion, IKalturaSearchDataContributor, IKalturaObjectLoader, IKalturaAdminConsolePages, IKalturaAdminConsoleEntryInvestigate, IKalturaPending, IKalturaMemoryCleaner, IKalturaConfigurator, IKalturaSchemaContributor
{
	const PLUGIN_NAME = 'contentDistribution';
	const PLUGIN_VERSION_MAJOR = 2;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const CONTENT_DSTRIBUTION_MANAGER = 'kContentDistributionFlowManager';
	const CONTENT_DSTRIBUTION_COPY_HANDLER = 'kContentDistributionObjectCopiedHandler';

	/* (non-PHPdoc)
	 * @see KalturaPlugin::getInstance()
	 */
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		if($interface == 'IKalturaMrssContributor')
			return kContentDistributionMrssManager::get();
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$dependency = new KalturaDependency(MetadataPlugin::getPluginName());
		return array($dependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
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
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
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
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::CONTENT_DSTRIBUTION_MANAGER,
			self::CONTENT_DSTRIBUTION_COPY_HANDLER,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ContentDistributionBatchJobType', 'ContentDistributionFileSyncObjectType');
	
		if($baseEnumName == 'BatchJobType')
			return array('ContentDistributionBatchJobType');
			
		if($baseEnumName == 'FileSyncObjectType')
			return array('ContentDistributionFileSyncObjectType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry)
			return kContentDistributionManager::getEntrySearchValues($object);
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
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
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
				return new kDistributionEnableJobData();
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
				return new kDistributionDisableJobData();
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
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
				return new KalturaDistributionEnableJobData();
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
				return new KalturaDistributionDisableJobData();
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
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
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
				return 'kDistributionEnableJobData';
				
			if($enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
				return 'kDistributionDisableJobData';
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT))
				return 'KalturaDistributionSubmitJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE))
				return 'KalturaDistributionUpdateJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE))
				return 'KalturaDistributionDeleteJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
				return 'KalturaDistributionFetchReportJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_ENABLE))
				return 'KalturaDistributionEnableJobData';
				
			if($enumValue == self::getApiValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE) || $enumValue == self::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DISABLE))
				return 'KalturaDistributionDisableJobData';
		}
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getAdminConsolePages()
	 */
	public static function getAdminConsolePages()
	{
		$pages = array();
		
		$pages[] = new DistributionProfileListAction();
		$pages[] = new DistributionProfileConfigureAction();
		$pages[] = new DistributionProfileUpdateStatusAction();
		
		$pages[] = new GenericDistributionProvidersListAction();
		$pages[] = new GenericDistributionProviderConfigureAction();
		$pages[] = new GenericDistributionProviderDeleteAction();
		
		return $pages;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsoleEntryInvestigate::getEntryInvestigatePlugins()
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

	/* (non-PHPdoc)
	 * @see IKalturaMemoryCleaner::cleanMemory()
	 */
	public static function cleanMemory()
	{
	    DistributionProfilePeer::clearInstancePool();
	    EntryDistributionPeer::clearInstancePool();
//	    GenericDistributionProviderPeer::clearInstancePool();
//	    GenericDistributionProviderActionPeer::clearInstancePool();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
	
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::isContributingToSchema()
	 */
	public static function isContributingToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		return ($coreType == SchemaType::SYNDICATION);  
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type, SimpleXMLElement $xsd)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != SchemaType::SYNDICATION)
			return;
			
		$import = $xsd->addChild('import');
		$import->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/" . self::getPluginName());
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function getPluginSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != SchemaType::SYNDICATION)
			return null;
			
		$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
		$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/" . self::getPluginName();
		
		$xsd = '<?xml version="1.0" encoding="UTF-8"?>
			<xs:schema 
				xmlns:xs="http://www.w3.org/2001/XMLSchema"
				xmlns="' . $xmlnsPlugin . '" 
				xmlns:core="' . $xmlnsBase . '" 
				targetNamespace="' . $xmlnsPlugin . '"
			>
				
				<xs:complexType name="T_distribution">
					<xs:sequence>
						<xs:element name="distributionProvider" minOccurs="1" maxOccurs="1" type="enums:KalturaDistributionProviderType" />
						<xs:element name="distributionProviderId" minOccurs="0" maxOccurs="1" type="xs:int" />
						<xs:element name="distributionProfileId" minOccurs="1" maxOccurs="1" type="xs:int" />
						<xs:element name="distributionProfile" minOccurs="1" maxOccurs="1" type="xs:string" />
						<xs:element name="distributionProfileName" minOccurs="0" maxOccurs="1" type="xs:string" />
						<xs:element name="remoteId" minOccurs="1" maxOccurs="1" type="xs:string" />
						<xs:element name="sunrise" minOccurs="0" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="sunset" minOccurs="0" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="flavorAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
							<xs:annotation>
								<xs:documentation>
									List of existing flavor asset ids to be used in this distribution destination.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
						<xs:element name="thumbAssetIds" minOccurs="0" maxOccurs="1" type="xs:string">
							<xs:annotation>
								<xs:documentation>
									List of existing thumbnail asset ids to be used in this distribution destination.
								</xs:documentation>
							</xs:annotation>
						</xs:element>
						<xs:element name="errorDescription" minOccurs="0" maxOccurs="1" type="xs:string" />
						<xs:element name="createdAt" minOccurs="1" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="updatedAt" minOccurs="1" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="submittedAt" minOccurs="0" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="lastReport" minOccurs="0" maxOccurs="1" type="xs:dateTime" />
						<xs:element name="dirtyStatus" minOccurs="0" maxOccurs="1" type="enums:KalturaEntryDistributionFlag" />
						<xs:element name="status" minOccurs="1" maxOccurs="1" type="enums:KalturaEntryDistributionStatus" />
						<xs:element name="sunStatus" minOccurs="1" maxOccurs="1" type="enums:KalturaEntryDistributionSunStatus" />
						<xs:element name="plays" minOccurs="0" maxOccurs="1" type="xs:int" />
						<xs:element name="views" minOccurs="0" maxOccurs="1" type="xs:int" />
						<xs:element name="errorNumber" minOccurs="0" maxOccurs="1" type="xs:int" />
						<xs:element name="errorType" minOccurs="0" maxOccurs="1" type="enums:KalturaBatchJobErrorTypes" />
					
						<xs:element ref="distribution-extension" minOccurs="0" maxOccurs="unbounded" />
						
					</xs:sequence>
					
					<xs:attribute name="entryDistributionId" use="required" type="xs:int" />
					
				</xs:complexType>
				
				<xs:element name="distribution" type="T_distribution" substitutionGroup="core:item-extension" />
				<xs:element name="distribution-extension" />
			</xs:schema>
		';
		
		return new SimpleXMLElement($xsd);
	}
}
