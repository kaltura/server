<?php
/**
 * @package plugins.cuePoint
 */
class CuePointSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaSphinxConfiguration
{
	const PLUGIN_NAME = 'cuePoint';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCriteriaFactory::getKalturaCriteria()
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == "CuePoint")
			return new SphinxCuePointCriteria();
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSphinxConfiguration::getSphinxConfigPath()
	 */
	public static function getSphinxConfigPath()
	{
		return dirname(__FILE__) . '/config/sphinx.conf'; 
	}
}
