<?php
/**
 * @package plugins.dropFolder
 */
class DropFolderPlugin extends KalturaPlugin implements IKalturaServices, IKalturaMemoryCleaner
{
	const PLUGIN_NAME = 'dropFolder';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
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
	

	public static function cleanMemory()
	{
	    DropFolderPeer::clearInstancePool();
	    DropFolderFilePeer::clearInstancePool();
	}
}
