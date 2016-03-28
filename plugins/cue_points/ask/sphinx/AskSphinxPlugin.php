<?php
/**
 * Enable indexing and searching answers cue point objects in sphinx
 * @package plugins.cuePoint
 */
class AskSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaPending
{
	const PLUGIN_NAME = 'askSphinx';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
	    $cuePointDependency = new KalturaDependency(CuePointPlugin::getPluginName());
	    $askDependency = new KalturaDependency(AskPlugin::getPluginName());
	
	    return array($cuePointDependency , $askDependency);
	}	
	
	/* (non-PHPdoc)
	 * @see IKalturaCriteriaFactory::getKalturaCriteria()
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == 'AnswerCuePoint')
			return new SphinxAnswerCuePointCriteria();
			
		return null;
	}
}
