<?php
/**
 * @package plugins.dropFolder
 */
class DropFolderPlugin extends KalturaPlugin implements IKalturaServices, IKalturaMemoryCleaner, IKalturaPermissions
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

}
