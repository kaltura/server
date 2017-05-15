<?php
/**
 * Class PollActions
 *
 * Package and location is not indicated
 *
 */


class PollActions
{

	/**
	 * @var PollCacheHandler
	 */
	private static $pollsCacheHandler;

	const ID_SEPARATOR_CHAR = '-';
	const ANSWER_SEPARATOR_CHAR = ';';
	const ID_NUM_ELEMENTS = 3;
	private static $pollTypes = null;

	private static $kalturaSecret = null;

	/* Configuration */

	private static function init()
	{
		self::$kalturaSecret = kConf::get("polls_secret");
		self::$pollsCacheHandler = new PollCacheHandler();
		self::$pollTypes = array( 'SINGLE_ANONYMOUS', 'SINGLE_RESTRICT' , 'MULTI_ANONYMOUS' , 'MULTI_RESTRICT'	);
	}

	/* Poll Id Action */
	public static function generatePollId($type = 'SINGLE_ANONYMOUS')
	{
		self::init();
		if (!self::isValidPollType($type))
			die('Poll type provided is invalid');
		$randKey = rand();
		$randKey=strval($randKey);
		$hash = hash_hmac('SHA256', self::$kalturaSecret, $randKey);
		return $type.self::ID_SEPARATOR_CHAR.$hash.self::ID_SEPARATOR_CHAR.$randKey;
	}

	private static function  isValidPollType($type)
	{
		return in_array($type, self::$pollTypes);
	}

	private static function isValidPollIdStructure($id)
	{
		self::init();
		$idElements = explode(self::ID_SEPARATOR_CHAR, $id);
		if (count($idElements) === self::ID_NUM_ELEMENTS ) {
			$pollType = $idElements[0];
			$hash = $idElements[1];
			$key = $idElements[2];
			$simulatedHash = hash_hmac('SHA256', self::$kalturaSecret, $key);
			$isHashOk = strcmp($hash, $simulatedHash) === 0;
			$validPollType=self::isValidPollType($pollType);
			return $isHashOk && $validPollType;

		}
		return false;
	}

	/* Poll Vote Actions */

	public static function setVote($pollId, $userId, $ansIds)
	{
		$answers = expolde(self::ANSWER_SEPARATOR_CHAR, $ansIds);
		if (self::isValidPollIdStructure($pollId)) {
			// check early user vote
			$result = self::$pollsCacheHandler->setCacheVote($userId, $pollId, $answers);
			if ($result) {
				$previousAnswers = expolde(self::ANSWER_SEPARATOR_CHAR, $result);
				self::$pollsCacheHandler->decrementAnsCounter($pollId, $previousAnswers);
			} else {
				self::$pollsCacheHandler->incrementPollVotersCount($pollId);
			}
			self::$pollsCacheHandler->increaseAnsCounter($pollId, $answers);
			return;
		}
		return "Failed to vote due to bad poll id structure";
		// TODO here we want to return 200 with Exception within
	}

	/* Poll Get Votes Actions */

	public static function getVotes($pollId, $ansIds)
	{
		self::init();
		$answers = explode(self::ANSWER_SEPARATOR_CHAR, $ansIds);
		$pollVotes = new PollVotes($pollId);

		$pollVotes->setNumVoters(self::$pollsCacheHandler->getPollVotersCount($pollId));
		foreach($answers as $ansId) {
			$answerCount = self::$pollsCacheHandler->getAnswerCounter($pollId, $ansId);
			$pollVotes->addAnswerCounter($ansId, $answerCount);
		}
		return $pollVotes->toJSONString();
	}

}

class PollCacheHandler
{

	// TODo - add initialization validations to class

	private $cache;

	public function __construct()
	{
		$this->cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
	}

	public function setCacheVote($userId, $pollId, $ansIds)
	{
		$userVoteKey = self::getPollUserVoteCacheKey($pollId, $userId);
		if ($this->cache->add($userVoteKey, $ansIds) === false) {
			$earlyVoteAnsIds = $this->cache->get($userVoteKey);
			$this->cache->set($userVoteKey, $ansIds);
			return $earlyVoteAnsIds;
		}
		return null;
	}

	public function getAnswerCounter($pollId, $ansId)
	{
		$key = $this->getPollAnswerCounterCacheKey($pollId, $ansId);
		$counter = $this->cache->get($key);
		if (!$counter)
			return 0;
		return $counter;
	}


	public function increaseAnsCounter($pollId, $ansIds)
	{
		//TODO add expiry to the poll counters keys
		foreach($ansIds as $ansId)
		{
			$ansCounterId = self::getPollAnswerCounterCacheKey($pollId, $ansId);
			// in case it does not exist it is set to the default init value (1 in this case)
			$this->cache->add($ansCounterId, 0);
			$this->cache->increment($ansCounterId);
		}
	}

	public function decrementAnsCounter($pollId, $ansIds)
	{
		foreach($ansIds as $ansId)
		{
			$ansCounterId = self::getPollAnswerCounterCacheKey($pollId, $ansId);
			$this->cache->decrement($ansCounterId);
		}

	}

	public function incrementPollVotersCount($pollId)
	{
		$this->cache->add($this->getPollVotersCacheKey($pollId), 0);
		$this->cache->increment($this->getPollVotersCacheKey($pollId));
	}

	public function getPollVotersCount($pollId)
	{
		return $this->cache->get($this->getPollVotersCacheKey($pollId));
	}

	/* Cache keys functions */
	private function getPollVotersCacheKey($pollId)
	{
		return $pollId . "_voters";
	}

	private function getPollUserVoteCacheKey($pollId, $userId)
	{
		return $pollId.'_'.$userId;
	}

	private function getPollAnswerCounterCacheKey($pollId, $ansId)
	{
		return $pollId.'_'.$ansId;
	}

}

class PollVotes {
	private $pollId = "";
	private $numVoters = 0 ;
	private $answerCounters = array();

	public function __construct($pollId)
	{
		$this->pollId = $pollId;
	}
	public function addAnswerCounter($ansId, $counter)
	{
		$this->answerCounters[$ansId] = $counter;
	}
	public function setNumVoters($voters)
	{
		if(!$voters)
			$this->numVoters = 0;
		else
			$this->numVoters = $voters;
	}
	public function toJSONString()
	{
		$pollIdText = '"PollId":' . $this->pollId;
		$numberOfVotersText = '"NumberOfVoters":' . $this->numVoters;
		$answersText = '"AnswersCounters":[';
		foreach ($this->answerCounters as $ans => $counter) {
			$answersText = $answersText.'"'.$ans.'":'.$counter.',';
		}
		if (count($this->answerCounters) > 0)
			$answersText = substr($answersText, 0, -1);
		$answersText = $answersText.']';
		return '{'.$pollIdText.','.$numberOfVotersText.','.$answersText.'}';

	}

}