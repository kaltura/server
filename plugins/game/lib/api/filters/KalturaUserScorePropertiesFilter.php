<?php
/**
 * @package plugins.game
 * @subpackage api.filters
 * @abstract
 * @relatedService UserScoreService
 */
class KalturaUserScorePropertiesFilter extends KalturaUserScorePropertiesBaseFilter
{
	/**
	 * @var int
	 */
	public $gameObjectId;
	
	/**
	 * @var KalturaGameObjectType
	 */
	public $gameObjectType;
	
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
	public $placesAboveUser;
	
	/**
	 * @var int
	 */
	public $placesBelowUser;
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function getCoreFilter()
	{
		return null;
	}
	
	protected function initGameServicesRedisInstance()
	{
		$redisWrapper = new kInfraRedisCacheWrapper();
		$redisConfig = kConf::get('game', kConfMapNames::REDIS);
		$config = array('host' => $redisConfig['host'], 'port' => $redisConfig['port'], 'timeout' => floatval($redisConfig['timeout']),
			'cluster' => $redisConfig['cluster'], 'persistent' => $redisConfig['persistent']);
		$redisWrapper->init($config);
		return $redisWrapper;
	}
	
	protected function prepareGameObjectKey()
	{
		if (is_null($this->gameObjectId))
		{
			throw new KalturaAPIException(KalturaErrors::GAME_OBJECT_ID_REQUIRED);
		}
		if (!$this->gameObjectType)
		{
			throw new KalturaAPIException(KalturaErrors::GAME_OBJECT_TYPE_REQUIRED);
		}
		
		$redisKey = kCurrentContext::getCurrentPartnerId();
		$redisKey.= '_' . $this->gameObjectId;
		KalturaLog::info("Accessing Redis game object: $redisKey");
		return $redisKey;
	}
	
	protected function updateRanksFromStartingRank($results, $startingRank)
	{
		$reorderedResults = array();
		foreach ($results as $userId => $score)
		{
			$reorderedResults[] = array('rank' => $startingRank, 'userId' => $userId, 'score' => $score);
			$startingRank++;
		}
		
		return $reorderedResults;
	}
	
	protected function getListByUserIdSubstring($redisWrapper, $redisKey)
	{
		$rangeResults = $redisWrapper->doZrevrange($redisKey, 0, -1);
		if (!$rangeResults)
		{
			return array();
		}
		
		$rangeResults = $this->updateRanksFromStartingRank($rangeResults, 0);
		
		$results = array();
		foreach ($rangeResults as $details)
		{
			if (strpos($details['userId'], $this->userIdIn) !== false)
			{
				$results[] = $details;
			}
		}
		
		return $results;
	}
	
	protected function getListBySpecificUserId($redisWrapper, $redisKey)
	{
		$userRank = $redisWrapper->doZrevrank($redisKey, $this->userIdEqual);
		$userScore = $redisWrapper->doZscore($redisKey, $this->userIdEqual);
		if ($userScore === false || $userRank === false)
		{
			return array();
		}
		
		$rankAbove = $userRank - $this->placesAboveUser;
		if ($rankAbove < 0)
		{
			$rankAbove = 0;
		}
		$rankBelow = $userRank + $this->placesBelowUser;
		
		$results = $redisWrapper->doZrevrange($redisKey, $rankAbove, $rankBelow);
		if (!$results)
		{
			$results = array();
		}
		
		$results = $this->updateRanksFromStartingRank($results, $rankAbove);
		
		return $results;
	}
	
	protected function paginateResults($pager, $results)
	{
		$pager->pageIndex = $pager->calcPageIndex();
		$pager->pageSize = $pager->calcPageSize();
		$start = ($pager->pageIndex - 1) * $pager->pageSize;
		$finish = $start + $pager->pageSize;
		
		if ($start < count($results))
		{
			$i = 0;
			$paginatedResults = array();
			foreach ($results as $result)
			{
				if ($i >= $start && $i < $finish)
				{
					$paginatedResults[] = $result;
				}
				$i++;
			}
			$results = $paginatedResults;
		}
		
		return $results;
	}
	
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$redisWrapper = $this->initGameServicesRedisInstance();
		
		$redisKey = $this->prepareGameObjectKey();
		
		if ($this->userIdIn)
		{
			$results = $this->getListByUserIdSubstring($redisWrapper, $redisKey);
		}
		elseif ($this->userIdEqual)
		{
			$results = $this->getListBySpecificUserId($redisWrapper, $redisKey);
		}
		else
		{
			$results = $redisWrapper->doZrevrange($redisKey, 0, -1);
			if ($results)
			{
				$results = $this->updateRanksFromStartingRank($results, 0);
			}
		}
		
		if ($results)
		{
			$results = $this->paginateResults($pager, $results);
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($results);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($results, $responseProfile);
		
		return $response;
	}
	
	public function updateUserScore($score)
	{
		$redisWrapper = $this->initGameServicesRedisInstance();
		
		$redisKey = $this->prepareGameObjectKey();
		
		if (!$this->userIdEqual)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_EQUAL_REQUIRED);
		}
		
		$redisWrapper->doZadd($redisKey, $score, $this->userIdEqual);
		$rank = $redisWrapper->doZrevrank($redisKey, $this->userIdEqual);
		$result = array(array('rank' => $rank, 'userId' => $this->userIdEqual, 'score' => $score));
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($result);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($result, null);
		
		return $response;
	}
}
