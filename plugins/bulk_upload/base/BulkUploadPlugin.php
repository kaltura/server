<?php

/**
 * This plugin replaces the deprecated BulkUploadService. It includes a service for uploading entries, categories, users and categoryUsers in bulks.
 *@package plugins.bulkUpload
 *
 */
class BulkUploadPlugin extends KalturaPlugin implements IKalturaConfigurator, IKalturaServices, IKalturaEventConsumers
{
    
    const PLUGIN_NAME = "bulkUpload";
    
	/* (non-PHPdoc)
     * @see IKalturaConfigurator::getConfig()
     */
    public static function getConfig ($configName)
    {
        if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
        
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


	/* (non-PHPdoc)
     * @see IKalturaEventConsumers::getEventConsumers()
     */
    public static function getEventConsumers ()
    {
        return array('kBatchJobLogManager');
    }


}
