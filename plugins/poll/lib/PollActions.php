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
	private $pollsCacheHandler;
	/**
	 * @var string
	 */
	private $kalturaPollSecret;
	/**
	 * @var int
	 */
	private $pollCacheTTL;
	/**
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->pollsCacheHandler = null;
		$this->kalturaPollSecret = null;
		$this->pollCacheTTL      = 0;

		$pollConf = kConf::get(PollActions::CONF_POLL_REF);
		if (array_key_exists(PollActions::CONF_SECRET_REF ,$pollConf))
			$this->kalturaPollSecret = $pollConf[PollActions::CONF_SECRET_REF];
		if (!$this->kalturaPollSecret)
			throw new Exception("Could not find polls_secret in the configuration");

		if (array_key_exists(PollActions::CONF_CACHE_TTL_REF, $pollConf))
			$this->pollCacheTTL = $pollConf[PollActions::CONF_CACHE_TTL_REF];
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_CACHE_ONLY_ACTIONS);
		if (!$cache)
			throw new Exception("Could not initiate cache instance (needed for polls)");

		$this->pollsCacheHandler = new PollCacheHandler($this->pollCacheTTL, $cache);
	}

	/* Poll Id Action */
	public function generatePollId($type = PollType::SINGLE_ANONYMOUS)
	{
		if (!PollType::isValidPollType($type))
			throw new Exception("Poll type provided is invalid");
		$randKey = rand();
		$hash = hash_hmac(PollActions::HASH_TYPE, $this->kalturaPollSecret, $randKey);
		return $type.self::ID_SEPARATOR_CHAR.$hash.self::ID_SEPARATOR_CHAR.$randKey;
	}

	private function isValidPollIdStructure($id)
	{
		$idElements = explode(self::ID_SEPARATOR_CHAR, $id);
		if (count($idElements) === self::ID_NUM_ELEMENTS )
		{
			$pollType = $idElements[0];
			$hash = $idElements[1];
			$key = $idElements[2];
			$simulatedHash = hash_hmac(PollActions::HASH_TYPE, $this->kalturaPollSecret, $key);
			$isHashOk = strcmp($hash, $simulatedHash) === 0;
			$validPollType = PollType::isValidPollType($pollType);
			return $isHashOk && $validPollType;
		}
		return false;
	}

	/* Poll Vote Actions to be called from cache */
	public static function vote($params)
	{
		if ( is_null($params) ||
			!array_key_exists(PollActions::POLL_ID_ARG, $params) ||
			!array_key_exists(PollActions::USER_ID_ARG, $params) ||
			!array_key_exists(PollActions::ANSWER_IDS_ARG, $params))
			return 'Missing parameter for vote action';


		$pollId     = $params[PollActions::POLL_ID_ARG];
		$ansIds     = $params[PollActions::ANSWER_IDS_ARG];
		$userId     = $params[PollActions::USER_ID_ARG];
		$ksUserId   = empty($params['___cache___userId']) ?  null : $params['___cache___userId'];
		$instance = new PollActions();
		$ret = $instance->setVote($pollId, $userId, $ksUserId ,$ansIds);
		return $ret;
	}

	private static function getValidUserId($pollType,$userId,$ksUserId)
	{
		$validUserId = $ksUserId;

		if(PollType::isAnonymous($pollType) and !$ksUserId)
				$validUserId = $userId;

		return $validUserId;
	}

	public function setVote($pollId, $userId, $ksUserId, $ansIds)
	{
		if ($this->isValidPollIdStructure($pollId))
		{
			$pollType = $this->getPollType($pollId);
			//validate User ID
			$userId = self::getValidUserId($pollType,$userId,$ksUserId);
			if (is_null($userId))
				return "User ID is invalid";

			//validate answers
			$answers = explode(self::ANSWER_SEPARATOR_CHAR, $ansIds);
			if(count($answers) > 1 and !PollType::isMultipleAnswer($pollType))
			{
				return "Only one answer is allowed";
			}
			$answers = array_unique($answers);

			// check early user vote
			$previousAnswers =$this->pollsCacheHandler->setCacheVote($userId, $pollId, $answers);
			if ($previousAnswers)
				$this->pollsCacheHandler->decrementAnswersCounter($pollId, $previousAnswers);
			else
				$this->pollsCacheHandler->incrementPollVotersCount($pollId);
			$this->pollsCacheHandler->incrementAnswersCounter($pollId, $answers);
			return "Successfully voted";
		}
		return "Failed to vote due to bad poll id structure";
	}

	private function getPollType($pollId)
	{
		$pollType = null;
		$idElements = explode(self::ID_SEPARATOR_CHAR, $pollId);
		if (count($idElements) === self::ID_NUM_ELEMENTS )
		{
			$pollType = isset($idElements[0]) ? $idElements[0] : null;
		}
		return $pollType;
	}

	public static function getVote($params)
	{
		if ( is_null($params) ||
			!array_key_exists(PollActions::POLL_ID_ARG, $params) ||
			!array_key_exists(PollActions::USER_ID_ARG, $params))
			return 'Missing parameter for get vote action';

		$pollId     = $params[PollActions::POLL_ID_ARG];
		$userId     = $params[PollActions::USER_ID_ARG];
		$ksUserId   = empty($params['___cache___userId']) ?  null : $params['___cache___userId'];

		$instance   = new PollActions();
		return $instance->doGetVote($pollId,$userId,$ksUserId);
	}

	/* get Vote Actions */
	public function doGetVote($pollId,$userId,$ksUserId)
	{
		if ($this->isValidPollIdStructure($pollId))
		{
			$pollType = $this->getPollType($pollId);
			$userId = self::getValidUserId($pollType,$userId,$ksUserId);
			$vote = $this->pollsCacheHandler->getCacheVote($userId, $pollId);
			if ($vote)
				return json_encode($vote);
			else
				return "Could not find vote for user id : $userId in poll id $pollId";
		}
		else
			return "Failed to get vote due to bad poll id structure";
	}


	/* Poll Get Votes Actions */

	public function getVotes($pollId, $ansIds)
	{
		if (!$pollId || !$ansIds)
			throw new Exception('Missing parameter for getVotes action');
		$answers = explode(self::ANSWER_SEPARATOR_CHAR, $ansIds);
		$pollVotes = new PollVotes($pollId);
		$pollVotes->setNumVoters($this->pollsCacheHandler->getPollVotersCount($pollId));
		foreach($answers as $ansId)
		{
			$answerCount = $this->pollsCacheHandler->getAnswerCounter($pollId, $ansId);
			$pollVotes->addAnswerCounter($ansId, $answerCount);
		}
		return $pollVotes;
	}

	public function resetVotes($pollId)
	{
		return $this->pollsCacheHandler->incrementCacheVersion($pollId);
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
		$userVoteKey = $this->getPollUserVoteCacheKey($pollId, $userId);
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
		$userVoteKey = $this->getPollUserVoteCacheKey($pollId, $userId);
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

	public function clearAnswersCounter($pollId, $ansIds)
	{
		foreach($ansIds as $ansId)
		{
			$ansCounterId = self::getPollAnswerCounterCacheKey($pollId, $ansId);
			$this->cache->set($ansCounterId,0,$this->cacheTTL);
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
		$version = $this->getCacheVersion($pollId);
		return $pollId .PollCacheHandler::CACHE_KEY_SEPARATOR.$version.PollCacheHandler::CACHE_KEY_SEPARATOR.PollCacheHandler::VOTERS_SUFFIX;
	}

	private function getPollUserVoteCacheKey($pollId, $userId)
	{
		$version = $this->getCacheVersion($pollId);
		return $pollId.PollCacheHandler::CACHE_KEY_SEPARATOR.$version.PollCacheHandler::CACHE_KEY_SEPARATOR.$userId;
	}

	private function getPollAnswerCounterCacheKey($pollId, $ansId)
	{
		$version = $this->getCacheVersion($pollId);
		return $pollId. PollCacheHandler::CACHE_KEY_SEPARATOR.$version.PollCacheHandler::CACHE_KEY_SEPARATOR.$ansId;
	}
	private function getCacheVersion($pollId)
	{
		$version = $this->cache->get($pollId);
		if(!$version)
			$version=0;

		return $version;
	}
	public function incrementCacheVersion($pollId)
	{
		$version = $this->getCacheVersion($pollId)+1;
		$this->cache->set($pollId,$version,$this->cacheTTL);

		return $version;
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

	public static function isAnonymous($type)
	{
		switch ($type)
		{
			case self::SINGLE_ANONYMOUS:
			case self::MULTI_ANONYMOUS:
				return true;
			default:
				return false;
		}
	}

	public static function isMultipleAnswer($type)
	{
		switch ($type)
		{
			case self::MULTI_ANONYMOUS:
			case self::MULTI_RESTRICT:
				return true;
			default:
				return false;
		}
	}
}

