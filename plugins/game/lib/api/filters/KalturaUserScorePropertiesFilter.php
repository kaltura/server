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
	 * Formats the results to a map with the expected properties, replacing kuser with puser in the results from 'mapKuserPuser',
	 * And adjusts the results ranks to be their real rank, instead of default Redis results starting from 0
	 * The results are returned as an array of maps
	 * @param $results
	 * @param $startingRank
	 * @param $mapKuserPuser
	 * @return array
	 */
	protected function formatUserScoreResults($results, $startingRank, $mapKuserPuser)
	{
		$adjustedResults = array();
		foreach ($results as $userId => $score)
		{
			$adjustedResults[] = array('rank' => $startingRank, 'userId' => $mapKuserPuser[$userId], 'score' => $score);
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
	 * @return array
	 */
	protected function getListBySpecificUserId($redisWrapper, $redisKey)
	{
		$kuserId = GamePlugin::getKuserIdFromPuserId($this->userIdEqual);
		
		// Redis returns 'false' if the provided userId does not exist
		$userRank = $redisWrapper->doZrevrank($redisKey, $kuserId);
		$userScore = $redisWrapper->doZscore($redisKey, $kuserId);
		if ($userScore === false || $userRank === false)
		{
			KalturaLog::info("No result found for userId {$this->userIdEqual} with key $redisKey");
			return array();
		}
		
		// Depending on the filter attributes, the query range around the user needs to be extended
		$startRank = $this->calculateStartRank($userRank);
		$endRank = $this->calculateEndRank($userRank);
		
		$results = $redisWrapper->doZrevrange($redisKey, $startRank, $endRank);
		if (!$results)
		{
			KalturaLog::info("No results found for key $redisKey with range $startRank, $endRank");
			$results = array();
		}
		
		$mapKuserPuser = GamePlugin::createMapKuserToPuser($results);
		
		$results = $this->formatUserScoreResults($results, $startRank, $mapKuserPuser);
		
		return $results;
	}
	
	/**
	 * @param $redisWrapper
	 * @param $pager
	 * @param $redisKey
	 * @return array
	 */
	protected function getListAllUsers($redisWrapper, $redisKey)
	{
		$results = $redisWrapper->doZrevrange($redisKey, 0, -1);
		if (!$results)
		{
			KalturaLog::info("No results found for key $redisKey");
			return array();
		}
		
		$mapKuserPuser = GamePlugin::createMapKuserToPuser($results);
		
		$results = $this->formatUserScoreResults($results, 0, $mapKuserPuser);
		
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
		$endRank = $startRank + $pager->pageSize;
		
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
		
		$redisKey = GamePlugin::prepareGameObjectKey($this->gameObjectId, $this->gameObjectType);
		
		
		if ($this->userIdEqual)
		{
			$results = $this->getListBySpecificUserId($redisWrapper, $redisKey);
		}
		else
		{
			$results = $this->getListAllUsers($redisWrapper, $redisKey);
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($results);
		$results = $this->paginateResults($pager, $results);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($results, $responseProfile);
		
		return $response;
	}
}
