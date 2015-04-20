<?php
/**
 * Enable defining quiz per entry
 * @package plugins.quiz
 */
class QuizPlugin extends KalturaPlugin implements IKalturaPending, IKalturaPermissions, IKalturaServices, IKalturaDynamicAttributeContributer
{
	const PLUGIN_NAME = "quiz";
	const QUESTION_ANSWER_PLUGIN_NAME = 'questionAnswer';
	const QUESTION_ANSWER_VERSION_MAJOR = 1;
	const QUESTION_ANSWER_VERSION_MINOR = 0;
	const QUESTION_ANSWER_VERSION_BUILD = 0;

	const IS_QUIZ = "isQuiz";
	const QUIZ_DATA = "quizData";

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName ()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	*/
	public static function dependsOn()
	{
		$questionAnswerVersion = new KalturaVersion(
			self::QUESTION_ANSWER_VERSION_MAJOR,
			self::QUESTION_ANSWER_VERSION_MINOR,
			self::QUESTION_ANSWER_VERSION_BUILD
		);

		$dependency = new KalturaDependency(self::QUESTION_ANSWER_PLUGIN_NAME, $questionAnswerVersion);
		return array($dependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap ()
	{
		$map = array(
			'quiz' => 'QuizService',
		);
		return $map;
	}

	/* (non-PHPdoc)
	 * @see IKalturaDynamicAttributeContributer::getDynamicAttribute()
	 */
	public static function getDynamicAttribute(entry $entry)
	{
		$isQuiz = 0;
		if ( !is_null($entry->getFromCustomData(self::QUIZ_DATA)) )
			$isQuiz = 1;

		$dynamicAttribute = array(self::getDynamicAttributeName() => $isQuiz);
		return $dynamicAttribute;
	}

	public static function getDynamicAttributeName()
	{
		return self::getPluginName() . '_' . self::IS_QUIZ;
	}

}