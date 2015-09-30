<?php
/**
 * Enable indexing and searching answers cue point objects in sphinx
 * @package plugins.cuePoint
 */
class AnswerCuePointSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory
{
	const PLUGIN_NAME = 'answerCuePointSphinx';
	
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
		if ($objectType == 'AnswerCuePoint')
			return new AnswerSphinxCuePointCriteria();
			
		return null;
	}
}
