<?php
/**
 * Enable indexing and searching answers cue point objects in sphinx
 * @package plugins.cuePoint
 */
class QuizSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaPending
{
	const PLUGIN_NAME = 'quizSphinx';
	
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
	    $quizDependency = new KalturaDependency(QuizPlugin::getPluginName());
	
	    return array($cuePointDependency , $quizDependency);
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
