<?php

/**
 * This plugin replaces the deprecated BulkUploadService. It includes a service for uploading entries, categories, users and categoryUsers in bulks.
 *@package plugins.bulkUploadCsv
 *
 */
class BulkUploadPlugin extends KalturaPlugin implements IKalturaConfigurator, IKalturaServices
{
    
    const PLUGIN_NAME = "bulkUpload";
	/* (non-PHPdoc)
     * @see IKalturaConfigurator::getConfig()
     */
    public static function getConfig ($configName)
    {
        // TODO Auto-generated method stub
        
    }


	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {
        return self::PLUGIN_NAME;
        
    }

    public static function getServicesMap()
	{
		$map = array(
			'bulk' => 'BulkService',
		);
		return $map;
	}
}
