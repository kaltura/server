<?php
/**
 * Class PollActions
 *
 * Package and location is not indicated
 * Should not include any kaltura dependency in this class - to enable it to run in cache only mode
 */
class PollActions
{

	const ID_SEPARATOR_CHAR = '-';
	const ANSWER_SEPARATOR_CHAR = ',';
	const ID_NUM_ELEMENTS = 3;

	const POLL_ID_ARG = 'pollId';
	const ANSWER_IDS_ARG = 'answerIds';
	const USER_ID_ARG = 'userId';

	const HASH_TYPE = 'SHA256';

	const CONF_SECRET_REF = 'secret';
	const CONF_CACHE_TTL_REF = 'cache_ttl';
	const CONF_POLL_REF = 'poll';

	/**
	 * @var PollCacheHandler
	 */
	private static $pollsCacheHandler = null;
	/**
	 * @var string
	 */
	private static $kalturaPollSecret = null;
	/**
	 * @var int
	 */
	private static $pollCacheTTL = 0;

	/* Configuration */
	/**
	 *
	 * @throws Exception
	 */
	private static function init()
	{
		$pollConf = kConf::get(PollActions::CONF_POLL_REF);
		if (array_key_exists(PollActions::CONF_SECRET_REF ,$pollConf))
			self::$kalturaPollSecret = $pollConf[PollActions::CONF_SECRET_REF];
		if (!self::$kalturaPollSecret)
			throw new Exception("Could not find polls_secret in the configuration");
		if (array_key_exists(PollActions::CONF_CACHE_TTL_REF, $pollConf))
			self::$pollCacheTTL = $pollConf[PollActions::CONF_CACHE_TTL_REF];
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_CACHE_ONLY_ACTIONS);
		if (!$cache)
			throw new Exception("Could not initiate cache instance (needed for polls)");
		self::$pollsCacheHandler = new PollCacheHandler(self::$pollCacheTTL, $cache);

	}

	/* Poll Id Action */
	public static function generatePollId($type = PollType::SINGLE_ANONYMOUS)
	{
		self::init();
		if (!PollType::isValidPollType($type))
			throw new Exception("Poll type provided is invalid");
		$randKey = rand();
		$hash = hash_hmac(PollActions::HASH_TYPE, self::$kalturaPollSecret, $randKey);
		return $type.self::ID_SEPARATOR_CHAR.$hash.self::ID_SEPARATOR_CHAR.$randKey;
	}

	private static function isValidPollIdStructure($id)
	{
		self::init();
		$idElements = explode(self::ID_SEPARATOR_CHAR, $id);
		if (count($idElements) === self::ID_NUM_ELEMENTS )
		{
			$pollType = $idElements[0];
			$hash = $idElements[1];
			$key = $idElements[2];
			$simulatedHash = hash_hmac(PollActions::HASH_TYPE, self::$kalturaPollSecret, $key);
			$isHashOk = strcmp($hash, $simulatedHash) === 0;
			$validPollType = PollType::isValidPollType($pollType);
			return $isHashOk && $validPollType;

		}
		return false;
	}

	/* Poll Vote Actions */
	public static function vote($params)
	{
		if ( is_null($params) ||
			!array_key_exists(PollActions::POLL_ID_ARG, $params) ||
			!array_key_exists(PollActions::USER_ID_ARG, $params) ||
			!array_key_exists(PollActions::ANSWER_IDS_ARG, $params))
			return 'Missing parameter for vote action';
		$pollId = $params[PollActions::POLL_ID_ARG];
		$userId = $params[PollActions::USER_ID_ARG];
		$ansIds = $params[PollActions::ANSWER_IDS_ARG];
		return self::setVote($pollId, $userId, $ansIds);
	}

	/* get Vote Actions */
	public static function getVote($params)
	{
		if ( is_null($params) ||
			!array_key_exists(PollActions::POLL_ID_ARG, $params) ||
			!array_key_exists(PollActions::USER_ID_ARG, $params))
			return 'Missing parameter for get vote action';
		$pollId = $params[PollActions::POLL_ID_ARG];
		$userId = $params[PollActions::USER_ID_ARG];
		if (self::isValidPollIdStructure($pollId))
		{
			$vote = self::$pollsCacheHandler->getCacheVote($userId, $pollId);
			if ($vote)
				return json_encode($vote);
			else
				return "Could not find vote for user id : $userId in poll id $pollId";
		}
		else
			return "Failed to get vote due to bad poll id structure";


	}

	public static function setVote($pollId, $userId, $ansIds)
	{
		if (self::isValidPollIdStructure($pollId))
		{
			$answers = explode(self::ANSWER_SEPARATOR_CHAR, $ansIds);
			$answers = array_unique($answers);
			// check early user vote
			$previousAnswers = self::$pollsCacheHandler->setCacheVote($userId, $pollId, $answers);
			if ($previousAnswers)
				self::$pollsCacheHandler->decrementAnswersCounter($pollId, $previousAnswers);
			else
				self::$pollsCacheHandler->incrementPollVotersCount($pollId);
			self::$pollsCacheHandler->incrementAnswersCounter($pollId, $answers);
			return "Successfully voted";
		}
		return "Failed to vote due to bad poll id structure";
	}

	/* Poll Get Votes Actions */

	public static function getVotes($pollId, $ansIds)
	{
		if (!$pollId || !$ansIds)
			throw new Exception('Missing parameter for getVotes action');
		self::init();
		$answers = explode(self::ANSWER_SEPARATOR_CHAR, $ansIds);
		$pollVotes = new PollVotes($pollId);
		$pollVotes->setNumVoters(self::$pollsCacheHandler->getPollVotersCount($pollId));
		foreach($answers as $ansId)
		{
			$answerCount = self::$pollsCacheHandler->getAnswerCounter($pollId, $ansId);
			$pollVotes->addAnswerCounter($ansId, $answerCount);
		}
		return $pollVotes;
	}

}

class PollCacheHandler
{

	const CACHE_KEY_SEPARATOR = '_';
	const VOTERS_SUFFIX = 'voters';

	private $cache;

	private $cacheTTL;

	public function __construct($cacheTTL, $cache)
	{
		$this->cache = $cache;
		$this->cacheTTL = $cacheTTL;
	}

	public function setCacheVote($userId, $pollId, $ansIds)
	{
		$userVoteKey = self::getPollUserVoteCacheKey($pollId, $userId);
		if (!$this->cache->add($userVoteKey, $ansIds, $this->cacheTTL))
		{
			$earlyVoteAnsIds = $this->cache->get($userVoteKey);
			$this->cache->set($userVoteKey, $ansIds);
			return $earlyVoteAnsIds;
		}
		return null;
	}

	public function getCacheVote($userId, $pollId)
	{
		$userVoteKey = self::getPollUserVoteCacheKey($pollId, $userId);
		return  $this->cache->get($userVoteKey);
	}

	public function getAnswerCounter($pollId, $ansId)
	{
		$key = $this->getPollAnswerCounterCacheKey($pollId, $ansId);
		$counter = $this->cache->get($key);
		if (!$counter)
			return 0;
		return $counter;
	}


	public function incrementAnswersCounter($pollId, $ansIds)
	{
		foreach($ansIds as $ansId)
		{
			$ansCounterId = self::getPollAnswerCounterCacheKey($pollId, $ansId);
			// in case it already exists the add function will not do anything
			$this->cache->add($ansCounterId, 0, $this->cacheTTL);
			$this->cache->increment($ansCounterId);
		}
	}

	public function decrementAnswersCounter($pollId, $ansIds)
	{
		foreach($ansIds as $ansId)
		{
			$ansCounterId = self::getPollAnswerCounterCacheKey($pollId, $ansId);
			$this->cache->decrement($ansCounterId);
		}
	}

	public function incrementPollVotersCount($pollId)
	{
		$this->cache->add($this->getPollVotersCacheKey($pollId), 0, $this->cacheTTL);
		$this->cache->increment($this->getPollVotersCacheKey($pollId));
	}

	public function getPollVotersCount($pollId)
	{
		$counter = $this->cache->get($this->getPollVotersCacheKey($pollId));
		if (!$counter)
			return 0;
		return $counter;
	}

	/* Cache keys functions */
	private function getPollVotersCacheKey($pollId)
	{
		return $pollId .PollCacheHandler::CACHE_KEY_SEPARATOR. PollCacheHandler::VOTERS_SUFFIX;
	}

	private function getPollUserVoteCacheKey($pollId, $userId)
	{
		return $pollId. PollCacheHandler::CACHE_KEY_SEPARATOR. $userId;
	}

	private function getPollAnswerCounterCacheKey($pollId, $ansId)
	{
		return $pollId. PollCacheHandler::CACHE_KEY_SEPARATOR. $ansId;
	}

}

class PollVotes {
	public $pollId;
	public $numVoters;
	public $answerCounters;

	public function __construct($pollId)
	{
		$this->pollId = $pollId;
		$this->numVoters = 0;
		$this->answerCounters = array();

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

	/**
	 * @param PollVotes $other
	 * @throws Exception
	 */
	public function merge($other)
	{
		if (!$other)
			throw new Exception("Can not merge Poll Votes with null object");
		$this->numVoters += $other->numVoters;
		foreach ($other->answerCounters as $ans => $counter)
		{
			$currentCounter = $this->answerCounters[$ans];
			if (isset($currentCounter))
				$this->answerCounters[$ans] = $currentCounter + $counter;
			else
				$this->answerCounters[$ans] = $counter;
		}
	}
}
class PollType {

	const SINGLE_ANONYMOUS = "SINGLE_ANONYMOUS";
	const SINGLE_RESTRICT = "SINGLE_RESTRICT";
	const MULTI_ANONYMOUS = "MULTI_ANONYMOUS";
	const MULTI_RESTRICT = "MULTI_RESTRICT";

	public static function isValidPollType($type)
	{
		switch ($type)
		{
			case self::SINGLE_ANONYMOUS:
			case self::SINGLE_RESTRICT:
			case self::MULTI_ANONYMOUS:
			case self::MULTI_RESTRICT:
				return true;
			default:
				return false;
		}
	}
}

