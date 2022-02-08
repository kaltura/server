<?php
/**
 * @package plugins.quiz
 * @subpackage quiz
 */
class kQuizManager implements kObjectChangedEventConsumer
{
	const EVENT_TYPE = 'eventType';
	const KUSER_ID = 'kuserId';
	const ENTRY_ID = 'entryId';
	const PARTNER_ID = 'partnerId';
	const EVENT_TIME = 'eventTime';
	const VIRTUAL_EVENT_ID = 'virtualEventId';
	const VERSION = 'version';
	const ANSWER_IDS = 'answerIds';
	const SCORE = 'score';
	const NUM_OF_CORRECT_ANSWERS = 'numOfCorrectAnswers';
	const NUM_OF_QUESTIONS = 'numOfQuestions';
	const NUM_OF_RELEVANT_QUESTIONS = 'numOfRelevnatQuestions';
	const CALCULATED_SCORE = 'calculatedScore';
	
	const MAP_NAME = 'internal_analytics_host';
	const HEADER_HOST = 'Host';
	const HEADER_IDENTIFIER = 'X-Forwarded-For';
	const HEADER_AGENT = 'User-Agent';
	const HEADER_AGENT_TYPE = 'HTTP_USER_AGENT';
	
	
	/*TODO:
	what should be the returned value?
	how to handle failure?
	what is the url?
	how to get the needed data for the headers?
	Do we need authentication?
	*/
	
	
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function objectChanged (BaseObject $object, array $modifiedColumns)
	{
		$quizEventContent = array();
		if (self::wasStatusChanged($object, $modifiedColumns))
		{
			$quizEventContent = self::generateQuizEventContent($object);
		}
		$uri = '/api_v3/index.php?' . http_build_query($quizEventContent, '', '&');
		
		self::sendBeacon($uri);
	}
	
	protected static function sendBeacon (string $uri)
	{
		$statsHost = explode(':', kConf::get(self::MAP_NAME));
		$host = $statsHost[0];
		$port = $statsHost[1];
		$headers = array(
			self::HEADER_HOST => $host,
			self::HEADER_IDENTIFIER => infraRequestUtils::getRemoteAddress(),);
		if (isset($_SERVER[self::HEADER_AGENT_TYPE]))
		{
			$headers[self::HEADER_AGENT] = $_SERVER[self::HEADER_AGENT_TYPE];
		}
		$out = "GET {$uri} HTTP/1.1\r\n";
		foreach($headers as $header => $value)
		{
			$out .= "$header: $value\r\n";
		}
		$out .= "\r\n";
		
		self::sendRequest($uri, $host, $port, $out);
		
	}
	
	protected static function sendRequest($uri, $host, $port, $out)
	{
		KalturaLog::info("Sending beacon to $uri");
		$fp = fsockopen($host, $port, $errorNumber, $errorMessage, 0.1);
		if ($fp === false)
		{
			KalturaLog::ERR("ERROR: Could not open socket connection [$host:$port] due to: [$errorNumber] $errorMessage");
		}
		
		fwrite($fp, $out);
		fclose($fp);
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
			&& in_array('user_entry.STATUS', $modifiedColumns)
			&& $object->getStatus() == 10060;
	}
	
	protected static function generateQuizEventContent($quizUserEntry)
	{
		/* @var $quizUserEntry QuizUserEntry */
		$quizEventContent = array(
			self::EVENT_TYPE => $quizUserEntry->getType(),
			self::KUSER_ID => $quizUserEntry->getkuser()->getId(),
			self::PARTNER_ID => $quizUserEntry->getPartnerId(),
			self::ENTRY_ID => $quizUserEntry->getEntryId(),
			self::EVENT_TIME => date('Y-m-d H:i:s'),//"2022-01-19T13:32:10Z" current time of this event being issued
			self::VIRTUAL_EVENT_ID => "Unknown",
			self::VERSION => $quizUserEntry->getVersion(),
			self::ANSWER_IDS => $quizUserEntry->getAnswerIds(),
			self::SCORE => $quizUserEntry->getScore(),
			self::NUM_OF_CORRECT_ANSWERS => $quizUserEntry->getNumOfCorrectAnswers(),
			self::NUM_OF_QUESTIONS => $quizUserEntry->getNumOfQuestions(),
			self::NUM_OF_RELEVANT_QUESTIONS => $quizUserEntry->getNumOfRelevnatQuestions(),
			self::CALCULATED_SCORE => $quizUserEntry->getCalculatedScore()
		);
		return $quizEventContent;
	}
	
}