<?php
/**
 * @package plugins.dropFolder
 */
class DropFolderPlugin extends KalturaPlugin implements IKalturaServices, IKalturaMemoryCleaner, IKalturaPermissions, IKalturaObjectLoader
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
				return new ContentDropFolderFileHandler();
			}
			if ($enumValue == DropFolderFileHandlerType::CSV)
			{
				return new CsvDropFolderFileHandler();
			}
			if ($enumValue == DropFolderFileHandlerType::XML)
			{
				return new XmlDropFolderFileHandler();
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
				return 'ContentDropFolderFileHandler';
			}
			if ($enumValue == DropFolderFileHandlerType::CSV)
			{
				return 'CsvDropFolderFileHandler';
			}
			if ($enumValue == DropFolderFileHandlerType::XML)
			{
				return 'XmlDropFolderFileHandler';
			}
		}
		
		return null;
	}
	

}
