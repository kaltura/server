<?php
/**
 * @package plugins.varConsole
 */
class VarConsolePlugin extends KalturaPlugin implements IKalturaConfigurator, IKalturaServices
{
    const PLUGIN_NAME = "varConsole";
    
	/* (non-PHPdoc)
     * @see IKalturaConfigurator::getConfig()
     */
    public static function getConfig ($configName)
    {
        if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
        
    }

	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {    
        return self::PLUGIN_NAME;
    }


	/* (non-PHPdoc)
     * @see IKalturaServices::getServicesMap()
     */
    public static function getServicesMap ()
    {
        $map = array(
			'varConsole' => 'VarConsoleService',
		);
		
		return $map;
    }



    
}