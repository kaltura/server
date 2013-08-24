<?php
/**
 * @package plugins.adminConsole
 */
class AdminConsolePlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaConfigurator, IKalturaPending
{
	const PLUGIN_NAME = 'adminConsole';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function dependsOn()
	{			
		$dependency = new KalturaDependency(FileSyncPlugin::getPluginName());
		return array($dependency);
	}
	
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}
		
	public static function getServicesMap()
	{
		$map = array(
			'entryAdmin' => 'EntryAdminService',
			'uiConfAdmin' => 'UiConfAdminService',
			'reportAdmin' => 'ReportAdminService',
		);
		return $map;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
	}
}
