<?php
/**
 * Add reports pages to the hosted pages application
 * @package plugins.hostedReports
 */
class HostedReportsPlugin extends KalturaPlugin implements IKalturaHostedPages, IKalturaConfigurator, IKalturaApplicationTranslations
{
	const PLUGIN_NAME = 'hostedReports';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaHostedPages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new CategoryMediaReportAction();
//		$pages[] = new MediaItemReportAction();
//		$pages[] = new UserMediaReportAction();
		return $pages;
	}
	
	private static function getTranslationsArray($locale)
	{
		$langFilePath = __DIR__ . "/config/lang/$locale.php";
		if(file_exists($langFilePath))
			return include($langFilePath);
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationTranslations::getTranslations()
	 */
	public static function getTranslations($locale)
	{
		return array($locale => self::getTranslationsArray($locale));
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
//		if($configName == 'hosted_pages')
//			return new Zend_Config_Ini(dirname(__FILE__) . '/config/hosted_pages.ini');
	}
}
