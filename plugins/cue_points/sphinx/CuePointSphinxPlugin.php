<?php
/**
 * Enable indexing and searching cue point objects in sphinx
 * @package plugins.cuePoint
 */
class CuePointSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaSphinxConfiguration
{
	const PLUGIN_NAME = 'cuePointSphinx';
	
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
	 * @see IKalturaSphinxConfiguration::getSphinxSchema()
	 */
	public static function getSphinxSchema(){
		return CuePointSphinxConfiguration::getConfiguration();
	}
}
