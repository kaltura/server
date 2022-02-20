<?php
/**
 * @package plugins.quiz
 * @subpackage quiz
 */
class kQuizManager implements kObjectChangedEventConsumer
{
	const EVENT_TYPE = 'eventType';
	const QUIZ_EVENT_TYPE = '@event_type_id@';
	const KUSER_ID = 'kuserId';
	const ENTRY_ID = 'entryId';
	const PARTNER_ID = 'partnerId';
	const VERSION = 'version';
	const ANSWER_IDS = 'answerIds';
	const SCORE = 'score';
	const NUM_OF_CORRECT_ANSWERS = 'numOfCorrectAnswers';
	const NUM_OF_QUESTIONS = 'numOfQuestions';
	const NUM_OF_RELEVANT_QUESTIONS = 'numOfRelevnatQuestions';
	const CALCULATED_SCORE = 'calculatedScore';
	const INTERNAL_ANALYTICS_HOST = 'internal_analytics_host';
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function objectChanged (BaseObject $object, array $modifiedColumns)
	{
		if (!self::wasStatusChanged($object, $modifiedColumns))
		{
			return false;
		}
		if (kConf::hasParam(self::INTERNAL_ANALYTICS_HOST))
		{
			$quizEventContent = self::getQuizEventContent($object);
			$statsHost = explode(':', kConf::get(self::INTERNAL_ANALYTICS_HOST));
			requestUtils::sendAnalyticsBeacon(
				$quizEventContent,
				$statsHost[0],
				isset($statsHost[1]) ? $statsHost[1] : 80);
		}
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
	{
		if (self::wasStatusChanged($object, $modifiedColumns))
		{
			return true;
		}
		return false;
	}
	
	protected static function wasStatusChanged(BaseObject $object, array $modifiedColumns)
	{
		return ($object instanceof QuizUserEntry)
			&& in_array(UserEntryPeer::STATUS, $modifiedColumns)
			&& $object->getStatus() == QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED);
	}
	
	protected static function getQuizEventContent($quizUserEntry)
	{
		/* @var $quizUserEntry QuizUserEntry */
		$contents = array(
			self::EVENT_TYPE => self::QUIZ_EVENT_TYPE,
			self::PARTNER_ID => $quizUserEntry->getPartnerId(),
			self::ENTRY_ID => $quizUserEntry->getEntryId(),
			self::VERSION => $quizUserEntry->getVersion(),
			self::ANSWER_IDS => $quizUserEntry->getAnswerIds(),
			self::SCORE => $quizUserEntry->getScore(),
			self::NUM_OF_CORRECT_ANSWERS => $quizUserEntry->getNumOfCorrectAnswers(),
			self::NUM_OF_QUESTIONS => $quizUserEntry->getNumOfQuestions(),
			self::NUM_OF_RELEVANT_QUESTIONS => $quizUserEntry->getNumOfRelevnatQuestions(),
			self::CALCULATED_SCORE => $quizUserEntry->getCalculatedScore()
		);
		if ($quizUserEntry->getkuser())
		{
			$contents[self::KUSER_ID] = $quizUserEntry->getkuser()->getId();
		}
		return $contents;
	}
	
}