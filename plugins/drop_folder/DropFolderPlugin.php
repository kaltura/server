<?php
/**
 * @package plugins.dropFolder
 */
class DropFolderPlugin extends KalturaPlugin implements IKalturaServices, IKalturaMemoryCleaner, IKalturaPermissions, IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'dropFolder';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function isAllowedPartner($partnerId)
	{
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
		if ($baseClass == 'DropFolderFileHandler')
		{
			if ($enumValue == DropFolderFileHandlerType::CONTENT)
			{
				return new DropFolderContentFileHandler();
			}
		}
		
		// drop folder does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
		
		if ($baseClass == 'KalturaDropFolderFileHandlerConfig')
		{
			if ($enumValue == KalturaDropFolderFileHandlerType::CONTENT)
			{
				return new KalturaDropFolderContentFileHandlerConfig();
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
		if ($baseClass == 'DropFolderFileHandler')
		{
			if ($enumValue == DropFolderFileHandlerType::CONTENT)
			{
				return 'DropFolderContentFileHandler';
			}
		}
		
		// drop folder does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;
		
		if ($baseClass == 'KalturaDropFolderFileHandlerConfig')
		{
			if ($enumValue == KalturaDropFolderFileHandlerType::CONTENT)
			{
				return 'KalturaDropFolderContentFileHandlerConfig';
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
			return array('DropFolderWatcherBatchType','DropFolderPermissionName');
			
		if($baseEnumName == 'BatchJobType')
			return array('DropFolderWatcherBatchType');
			
		if($baseEnumName == 'PermissionName')
			return array('DropFolderPermissionName');
			
		return array();
	}	

}
