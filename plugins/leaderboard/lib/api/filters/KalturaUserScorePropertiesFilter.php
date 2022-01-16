<?php
/**
 * @package plugins.leaderboard
 * @subpackage api.filters
 * @abstract
 * @relatedService UserScoreService
 */
class KalturaUserScorePropertiesFilter extends KalturaUserScorePropertiesBaseFilter
{
	static private $map_between_objects = array
	(
		"userIdEqual" => "_eq_puser_id",
		"userIdIn" => "_in_puser_id",
		"rankGreaterThanOrEqual" => "_gte_rank",
		"rankLessThanOrEqual" => "_lte_rank",
	);
	
	static private $order_by_map = array
	(
		"+rank" => "+rank",
		"-rank" => "-rank",
	);
	
	/**
	 * @var int
	 */
	public $virtualEventId;
	
	/**
	 * @var int
	 */
	public $leaderboardId;
	
	/**
	 * @var string
	 */
	public $userIdEqual;
	
	/**
	 * @var string
	 */
	public $userIdIn;
	
	/**
	 * @var int
	 */
	public $ranksAboveUser;
	
	/**
	 * @var int
	 */
	public $ranksBelowUser;
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function getCoreFilter()
	{
		return null;
	}
	
	private function initRedisWrapper()
	{
		$redisWrapper = new kInfraRedisCacheWrapper();
		$redisConfig = kConf::get('redis', kConfMapNames::RUNTIME_CONFIG);
		$config = array('host' => $redisConfig['host'], 'port' => $redisConfig['port'], 'timeout' => '0');
		$redisWrapper->init($config);
		return $redisWrapper;
	}
	
	private function prepareRedisKey()
	{
		$redisKey = kCurrentContext::getCurrentPartnerId();
		if ($this->virtualEventId)
		{
			$redisKey.= '_' . $this->virtualEventId;
			if ($this->leaderboardId)
			{
				$redisKey.= '_' . $this->leaderboardId;
			}
		}
		return $redisKey;
	}
	
	private function addRanksToRange($rangeResults, $startingRank)
	{
		$results = array();
		foreach ($rangeResults as $userId => $score)
		{
			$results[$startingRank] = array('userId' => $userId, 'score' => $score);
			$startingRank++;
		}
		
		return $results;
	}
	
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$redisWrapper = $this->initRedisWrapper();
		
		$redisKey = $this->prepareRedisKey();
		
		if ($this->userIdIn)
		{
			$tempResults = $redisWrapper->doZrevrange($redisKey, 0, -1);
			$tempResults = $this->addRanksToRange($tempResults, 0);
			$results = array();
			foreach ($tempResults as $key => $value)
			{
				if (strpos($value['userId'], $this->userIdIn) !== false)
				{
					$results[$key] = $value;
				}
			}
		}
		elseif ($this->userIdEqual)
		{
			$userRank = $redisWrapper->doZrevrank($redisKey, $this->userIdEqual);
			$userScore = $redisWrapper->doZscore($redisKey, $this->userIdEqual);
			$results[$userRank] = array('userId' => $this->userIdEqual, 'score' => $userScore);
			if ($this->ranksAboveUser)
			{
				$rankAbove = $userRank - $this->ranksAboveUser;
				if ($rankAbove < 0)
				{
					$rankAbove = 0;
				}
				$resultsAbove = $redisWrapper->doZrevrange($redisKey, $rankAbove, $userRank - 1);
				$resultsAbove = $this->addRanksToRange($resultsAbove, $rankAbove);
				foreach ($resultsAbove as $key => $value)
				{
					$results[$key] = $value;
				}
			}
			if ($this->ranksBelowUser)
			{
				$rankBelow = $userRank + $this->ranksBelowUser;
				$resultsBelow = $redisWrapper->doZrevrange($redisKey, $userRank + 1, $rankBelow);
				$resultsBelow = $this->addRanksToRange($resultsBelow, $userRank + 1);
				foreach ($resultsBelow as $key => $value)
				{
					$results[$key] = $value;
				}
			}
		}
		else
		{
			$tempResults = $redisWrapper->doZrevrange($redisKey, 0, -1);
			$results = $this->addRanksToRange($tempResults, 0);
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($results);
		ksort($results);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($results, $responseProfile);
		
		return $response;
	}
	
	public function updateUserScore(int $score)
	{
		$redisWrapper = $this->initRedisWrapper();
		
		$redisKey = $this->prepareRedisKey();
		
		if (!$this->userIdEqual)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_EQUAL_REQUIRED);
		}
		
		$redisWrapper->doZadd($redisKey, $score, $this->userIdEqual);
		$rank = $redisWrapper->doZrevrank($redisKey, $this->userIdEqual);
		$score = $redisWrapper->doZscore($redisKey, $this->userIdEqual);
		$array = array($rank => array('userId' => $this->userIdEqual, 'score' => $score));
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($array);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($array, null);
		
		return $response;
	}
}
