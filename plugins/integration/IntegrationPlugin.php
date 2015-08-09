<?php
/**
 * @package plugins.integration
 */
class IntegrationPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEventConsumers, IKalturaEnumerator, IKalturaVersion, IKalturaPending, IKalturaConfigurator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'integration';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const INTEGRATION_FLOW_MANAGER = 'kIntegrationFlowManager';
	
	const EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME = "EXTERNAL_INTEGRATION_SERVICES_ROLE";
	
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
			'integration' => 'IntegrationService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::INTEGRATION_FLOW_MANAGER,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('IntegrationBatchJobType');
	
		if($baseEnumName == 'BatchJobType')
			return array('IntegrationBatchJobType');
			
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
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{			
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		if (is_null($objectClass)) 
		{
			return null;
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
				return 'kIntegrationJobData';
		}
	
		if($baseClass == 'KalturaJobData')
		{
			if($enumValue == self::getApiValue(IntegrationBatchJobType::INTEGRATION) || $enumValue == self::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
				return 'KalturaIntegrationJobData';
		}
		
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
	
	/**
	 * @return string
	 */
	public static function generateKs($partnerId, $entryId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$userSecret = $partner->getSecret();
	
		//actionslimit:1
		$privileges = kSessionBase::PRIVILEGE_SET_ROLE . ":" . self::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME;
		$privileges .= "," . kSessionBase::PRIVILEGE_ACTIONS_LIMIT . ":1";
		
		$dcParams = kDataCenterMgr::getCurrentDc();
		$token = $dcParams["secret"];
		$additionalData = md5($entryId . $token);
		
		$ks = "";
		$creationSucces = kSessionUtils::startKSession ($partnerId, $userSecret, "", $ks, 86400, KalturaSessionType::USER, "", $privileges, null,$additionalData);
		if ($creationSucces >= 0 )	
			return $ks;
	
		return false;
	}
}
