<?php
/**
 * @package plugins.dropFolder
 */
class DropFolderPlugin extends KalturaPlugin implements IKalturaServices, IKalturaMemoryCleaner, IKalturaPermissions, IKalturaObjectLoader, IKalturaEnumerator, IKalturaAdminConsolePages, IKalturaConfigurator, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'dropFolder';
	const DROP_FOLDER_EVENTS_CONSUMER = 'kDropFolderEventsConsumer';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);		
	}
	
	public static function cleanMemory()
	{
		DropFolderPeer::clearInstancePool();
	    DropFolderFilePeer::clearInstancePool();		
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'dropFolder' => 'DropFolderService',
			'dropFolderFile' => 'DropFolderFileService',
		);
		return $map;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{			
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		
		if (is_null($objectClass)) {
			return null;
		}
		
		if (!is_null($constructorArgs) && $objectClass != 'KalturaDropFolderContentProcessorJobData')
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{	
		if ($baseClass == 'DropFolder')
		{
		    if ($enumValue == DropFolderType::LOCAL)
			{
				return 'DropFolder';
			}
			if ($enumValue == DropFolderType::FTP)
			{
				return 'FtpDropFolder';
			}
			if ($enumValue == DropFolderType::SCP)
			{
				return 'ScpDropFolder';
			}
			if ($enumValue == DropFolderType::SFTP)
			{
				return 'SftpDropFolder';
			}
			if ($enumValue == DropFolderType::SFTP_CMD)
			{
				return 'SftpDropFolder';
			}
			if ($enumValue == DropFolderType::SFTP_SEC_LIB)
			{
				return 'SftpDropFolder';
			}
		}
		
		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_DropFolder_Type_DropFolder')
    		{
    		    if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::LOCAL)
    			{
    				return 'Kaltura_Client_DropFolder_Type_DropFolder';
    			}    		    
    		    if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::FTP)
    			{
    				return 'Kaltura_Client_DropFolder_Type_FtpDropFolder';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SCP)
    			{
    				return 'Kaltura_Client_DropFolder_Type_ScpDropFolder';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP)
    			{
    				return 'Kaltura_Client_DropFolder_Type_SftpDropFolder';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP_CMD)
    			{
    				return 'Kaltura_Client_DropFolder_Type_SftpDropFolder';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP_SEC_LIB)
    			{
    				return 'Kaltura_Client_DropFolder_Type_SftpDropFolder';
    			}  			
    		}
    		
    		if ($baseClass == 'Form_DropFolderConfigureExtend_SubForm')
    		{
    		    if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::FTP)
    			{
    				return 'Form_FtpDropFolderConfigureExtend_SubForm';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SCP)
    			{
    				return 'Form_ScpDropFolderConfigureExtend_SubForm';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP)
    			{
    				return 'Form_SftpDropFolderConfigureExtend_SubForm';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP_CMD)
    			{
    				return 'Form_SftpDropFolderConfigureExtend_SubForm';
    			}
    			if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::SFTP_SEC_LIB)
    			{
    				return 'Form_SftpDropFolderConfigureExtend_SubForm';
    			}
    		}	
		}
		
		if ($baseClass == 'KalturaDropFolderFileHandlerConfig')
		{
			if ($enumValue == KalturaDropFolderFileHandlerType::CONTENT)
			{
				return 'KalturaDropFolderContentFileHandlerConfig';
			}
		}

		if ($baseClass == 'KalturaDropFolder')
		{
		    if ($enumValue == KalturaDropFolderType::LOCAL)
			{
				return 'KalturaDropFolder';
			}
		    if ($enumValue == KalturaDropFolderType::FTP)
			{
				return 'KalturaFtpDropFolder';
			}
			if ($enumValue == KalturaDropFolderType::SCP)
			{
				return 'KalturaScpDropFolder';
			}
			if ($enumValue == KalturaDropFolderType::SFTP)
			{
				return 'KalturaSftpDropFolder';
			}
			if ($enumValue == KalturaDropFolderType::SFTP_CMD)
			{
				return 'KalturaSftpDropFolder';
			}
			if ($enumValue == KalturaDropFolderType::SFTP_SEC_LIB)
			{
				return 'KalturaSftpDropFolder';
			}
		}
		
		if ($baseClass == 'KalturaImportJobData')
		{
		    if ($enumValue == 'kDropFolderImportJobData')
			{
				return 'KalturaDropFolderImportJobData';
			}
		}
		
		if ($baseClass == 'KalturaJobData')
		{
			KalturaLog::debug("in KalturaJobData checking for enum");
		    if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR))
			{
				return 'KalturaDropFolderContentProcessorJobData';
			}
		}
			
		if ($baseClass == 'KDropFolderFileHandler')
		{
			if ($enumValue == KalturaDropFolderFileHandlerType::CONTENT)
			{
				return 'KDropFolderContentFileHandler';
			}
			
		}
		
		return null;
	}
	
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DropFolderBatchType','DropFolderPermissionName', 'DropFolderBatchJobObjectType');
			
		if($baseEnumName == 'BatchJobType')
			return array('DropFolderBatchType');
			
		if($baseEnumName == 'PermissionName')
			return array('DropFolderPermissionName');
			
		if($baseEnumName == 'BatchJobObjectType')
			return array('DropFolderBatchJobObjectType');
			
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new DropFolderListAction();
		$pages[] = new DropFolderConfigureAction();
		$pages[] = new DropFolderSetStatusAction();
		return $pages;
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
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::DROP_FOLDER_EVENTS_CONSUMER,
		);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCoreValue($type, $valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore($type, $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
}
