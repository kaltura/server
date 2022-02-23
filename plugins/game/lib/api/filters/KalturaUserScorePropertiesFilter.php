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
	
	/**
	 * Prepare the redis key to be called with
	 * @return string
	 * @throws KalturaAPIException
	 */
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
	
	protected function getKUser($redisWrapper, $pUser)
	{
		$partner = kCurrentContext::getCurrentPartnerId();
		$kUser = kuserPeer::getKuserByPartnerAndUid($partner, $pUser);
		if (!$kUser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $pUser);
		}
		
		return $kUser->getId();
	}
	
	/**
	 * Retrieves pUsers for all kUsers in the results array, and returns a map for these pUsers
	 * @param $results
	 * @return array
	 * @throws PropelException
	 */
	protected function createMapKUserToPUser($results)
	{
		$kUsers = array_keys($results);
		
		$pUsers = kuserPeer::retrieveByPKs($kUsers);
		if (!$pUsers)
		{
			KalturaLog::info('Failed to retrieve pUsers from DB');
			return array();
		}
		
		$mapKUserPUser = array();
		foreach ($pUsers as $pUser)
		{
			$mapKUserPUser[$pUser->getId()] = $pUser->getPuserId();
		}
		
		return $mapKUserPUser;
	}
	
	/**
	 * Adjust the results' ranks to be their real rank, instead of default Redis results starting from 0
	 * The adjusted results are returned as an array of maps
	 * @param $results
	 * @param $startingRank
	 * @param $mapKUserPUser
	 * @return array
	 */
	protected function adjustRanksBasedOnStartingRank($results, $startingRank, $mapKUserPUser)
	{
		$adjustedResults = array();
		foreach ($results as $userId => $score)
		{
			$adjustedResults[] = array('rank' => $startingRank, 'userId' => $mapKUserPUser[$userId], 'score' => $score);
			$startingRank++;
		}
		
		return $adjustedResults;
	}
	
	/**
	 * @param $userRank
	 * @return int
	 */
	protected function calculateStartRank($userRank)
	{
		$startRank = $userRank - $this->placesAboveUser;
		if ($startRank < 0)
		{
			$startRank = 0;
		}
		return $startRank;
	}
	
	/**
	 * @param $userRank
	 * @return int
	 */
	protected function calculateEndRank($userRank)
	{
		return $userRank + $this->placesBelowUser;
	}
	
	/**
	 * @param $redisWrapper
	 * @param $pager
	 * @param $redisKey
	 * @return array|mixed
	 */
	protected function getListByUserIdSubstring($redisWrapper, $pager, $redisKey)
	{
		$rangeResults = $redisWrapper->doZrevrange($redisKey, 0, -1);
		if (!$rangeResults)
		{
			KalturaLog::info("No results found for key $redisKey with range 0, -1");
			return array();
		}
		
		$rangeResults = $this->adjustRanksBasedOnStartingRank($rangeResults, 0);
		
		$results = array();
		foreach ($rangeResults as $details)
		{
			// Check for puser  ?? //
			if (strpos($details['userId'], $this->userIdIn) !== false)
			{
				$results[] = $details;
			}
		}
		
		$results = $this->paginateResults($pager, $results);
		
		return $results;
	}
	
	/**
	 * @param $redisWrapper
	 * @param $pager
	 * @param $redisKey
	 * @return array
	 */
	protected function getListBySpecificUserId($redisWrapper, $pager, $redisKey)
	{
		$kUser = $this->getKUser($redisWrapper, $this->userIdEqual);
		
		// Redis returns 'false' if the provided userId does not exist
		$userRank = $redisWrapper->doZrevrank($redisKey, $kUser);
		$userScore = $redisWrapper->doZscore($redisKey, $kUser);
		if ($userScore === false || $userRank === false)
		{
			KalturaLog::info("No result found for userId {$this->userIdEqual} with key $redisKey");
			return array();
		}
		
		// Depending on the filter attributes, the query range around the user needs to be extended
		$startRank = $this->calculateStartRank($userRank);
		$endRank = $this->calculateEndRank($userRank);
		
		// Adjust the query range according to the pager
		$startRankPager = ($pager->pageIndex - 1) * $pager->pageSize;
		$endRankPager = $startRank + $pager->pageSize - 1;
		if ($endRank - $startRank >= $startRankPager && $pager->pageSize != 0)
		{
			$startRank += $startRankPager;
			if ($endRank > $startRank + $endRankPager)
			{
				$endRank = $startRank + $endRankPager;
			}
		}
		else
		{
			return array();
		}
		
		$results = $redisWrapper->doZrevrange($redisKey, $startRank, $endRank);
		if (!$results)
		{
			KalturaLog::info("No results found for key $redisKey with range $startRank, $endRank");
			$results = array();
		}
		
		$mapKUserPUser = $this->createMapKUserToPUser($results);
		
		$results = $this->adjustRanksBasedOnStartingRank($results, $startRank, $mapKUserPUser);
		
		return $results;
	}
	
	/**
	 * @param $redisWrapper
	 * @param $pager
	 * @param $redisKey
	 * @return array
	 */
	protected function getListAllUsers($redisWrapper, $pager, $redisKey)
	{
		$startRank = ($pager->pageIndex - 1) * $pager->pageSize;
		$endRank = $startRank + $pager->pageSize - 1;
		
		$results = $redisWrapper->doZrevrange($redisKey, $startRank, $endRank);
		if (!$results)
		{
			KalturaLog::info("No results found for key $redisKey with range $startRank, $endRank");
			return array();
		}
		
		$mapKUserPUser = $this->createMapKUserToPUser($results);
		
		$results = $this->adjustRanksBasedOnStartingRank($results, $startRank, $mapKUserPUser);
		
		return $results;
	}
	
	/**
	 * @param $pager
	 * @param $results
	 * @return array|mixed
	 */
	protected function paginateResults($pager, $results)
	{
		$startRank = ($pager->pageIndex - 1) * $pager->pageSize;
		$endRank = $startRank + $pager->pageSize - 1;
		
		if ($startRank < count($results))
		{
			$i = 0;
			$paginatedResults = array();
			foreach ($results as $result)
			{
				if ($i >= $startRank && $i < $endRank)
				{
					$paginatedResults[] = $result;
				}
				$i++;
			}
			$results = $paginatedResults;
		}
		
		return $results;
	}
	
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile|null $responseProfile
	 * @return KalturaUserScorePropertiesResponse
	 * @throws KalturaAPIException
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$redisWrapper = GamePlugin::initGameServicesRedisInstance();
		if (!$redisWrapper)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_INIT_REDIS_INSTANCE);
		}
		
		$redisKey = $this->prepareGameObjectKey();
		
		if ($this->userIdIn)
		{
			$results = $this->getListByUserIdSubstring($redisWrapper, $pager, $redisKey);
		}
		elseif ($this->userIdEqual)
		{
			$results = $this->getListBySpecificUserId($redisWrapper, $pager, $redisKey);
		}
		else
		{
			$results = $this->getListAllUsers($redisWrapper, $pager, $redisKey);
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($results);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($results, $responseProfile);
		
		return $response;
	}
	
	/**
	 * @param $score
	 * @return KalturaUserScorePropertiesResponse
	 * @throws KalturaAPIException
	 */
	public function updateUserScore($score)
	{
		$redisWrapper = GamePlugin::initGameServicesRedisInstance();
		if (!$redisWrapper)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_INIT_REDIS_INSTANCE);
		}
		
		$redisKey = $this->prepareGameObjectKey();
		
		if (!$this->userIdEqual)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_EQUAL_REQUIRED);
		}
		
		$kUser = $this->getKUser($redisWrapper, $this->userIdEqual);
		
		$addResult = $redisWrapper->doZadd($redisKey, $score, $kUser);
		$rank = $redisWrapper->doZrevrank($redisKey, $kUser);
		if ($addResult === false || $rank === false)
		{
			KalturaLog::info("Failed to add {$this->userIdEqual} to key $redisKey");
			$result = array();
		}
		else
		{
			$result = array(array('rank' => $rank, 'userId' => $this->userIdEqual, 'score' => $score));
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($result);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($result, null);
		
		return $response;
	}
}
