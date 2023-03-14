<?php
/**
 * @package plugins.quiz
 * @subpackage quiz
 */
class kQuizManager implements kObjectChangedEventConsumer
{
	const SERVICE = 'service';
	const ACTION = 'action';
	const SERVICE_ANALYTICS = 'analytics';
	const ACTION_TRACKEVENT = 'trackEvent';
	const EVENT_TYPE = 'eventType';
	const QUIZ_EVENT_TYPE = 30001;
	const USER_ID = 'userId';
	const ENTRY_ID = 'entryId';
	const PARTNER_ID = 'partnerId';
	const VERSION = 'version';
	const ANSWER_IDS = 'answerIds';
	const SCORE = 'score';
	const NUM_OF_CORRECT_ANSWERS = 'numOfCorrectAnswers';
	const NUM_OF_QUESTIONS = 'numOfQuestions';
	const NUM_OF_RELEVANT_QUESTIONS = 'numOfRelevnatQuestions';
	const CALCULATED_SCORE = 'calculatedScore';
	const VIRTUAL_EVENT_ID = 'virtualEventId';
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
			self::SERVICE => self::SERVICE_ANALYTICS,
			self::ACTION => self::ACTION_TRACKEVENT,
			self::EVENT_TYPE => self::QUIZ_EVENT_TYPE,
			self::PARTNER_ID => $quizUserEntry->getPartnerId(),
			self::ENTRY_ID => $quizUserEntry->getEntryId(),
			self::VERSION => $quizUserEntry->getVersion(),
			self::ANSWER_IDS => $quizUserEntry->getAnswerIds(),
			self::SCORE => self::normalizeScore($quizUserEntry->getScore()),
			self::NUM_OF_CORRECT_ANSWERS => $quizUserEntry->getNumOfCorrectAnswers(),
			self::NUM_OF_QUESTIONS => $quizUserEntry->getNumOfQuestions(),
			self::NUM_OF_RELEVANT_QUESTIONS => $quizUserEntry->getNumOfRelevnatQuestions(),
			self::CALCULATED_SCORE => self::normalizeScore($quizUserEntry->getCalculatedScore()),
			self::VIRTUAL_EVENT_ID => kCurrentContext::$virtual_event_id
		);
		if ($quizUserEntry->getkuser())
		{
			$kuser = kuserPeer::retrieveByPKNoFilter($quizUserEntry->getkuser()->getId());
			if ($kuser)
			{
				$contents[self::USER_ID] = $kuser->getPuserId();
			}
		}
		return $contents;
	}
	
	protected static function normalizeScore($score)
	{
		return round($score * 100, 0 , PHP_ROUND_HALF_UP);
	}
	
}