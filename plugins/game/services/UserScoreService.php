<?php

/**
 * @service userScore
 * @package plugins.game
 * @subpackage api.services
 */
class UserScoreService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!GamePlugin::isAllowedPartner($partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, "{$this->serviceName}->{$this->actionName}");
		}
	}
	
	/**
	 * @action list
	 * @param KalturaUserScorePropertiesFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function listAction(KalturaUserScorePropertiesFilter $filter, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			throw new KalturaAPIException(KalturaErrors::USER_SCORE_PROPERTIES_FILTER_REQUIRED);
		}
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		$pager->pageIndex = $pager->calcPageIndex();
		$pager->pageSize = $pager->calcPageSize();
		
		return $filter->getListResponse($pager);
	}
	
	/**
	 * @action update
	 * @param int $gameObjectId
	 * @param KalturaGameObjectType $gameObjectType
	 * @param string $userId
	 * @param int $score
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function updateAction($gameObjectId, $gameObjectType, $userId, $score)
	{
		$redisWrapper = GamePlugin::initGameServicesRedisInstance();
		if (!$redisWrapper)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_INIT_REDIS_INSTANCE);
		}
		
		$redisKey = GamePlugin::prepareGameObjectKey($gameObjectId, $gameObjectType);
		
		if (!$userId)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_EQUAL_REQUIRED);
		}
		
		$kuserId = GamePlugin::getKuserIdFromPuserId($userId);
		
		$addResult = $redisWrapper->doZadd($redisKey, $score, $kuserId);
		$rank = $redisWrapper->doZrevrank($redisKey, $kuserId);
		if ($addResult === false || $rank === false)
		{
			KalturaLog::info("Failed to add $userId to key $redisKey");
			$result = array();
		}
		else
		{
			$result = array(array('rank' => $rank, 'userId' => $userId, 'score' => $score));
		}
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($result);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($result, null);
		
		return $response;
	}
	
	/**
	 * @action delete
	 * @param int $gameObjectId
	 * @param KalturaGameObjectType $gameObjectType
	 * @param string $userId
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function deleteAction($gameObjectId, $gameObjectType, $userId)
	{
		$redisWrapper = GamePlugin::initGameServicesRedisInstance();
		if (!$redisWrapper)
		{
			throw new KalturaAPIException(KalturaErrors::FAILED_INIT_REDIS_INSTANCE);
		}
		
		$redisKey = GamePlugin::prepareGameObjectKey($gameObjectId, $gameObjectType);
		
		if (!$userId)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_EQUAL_REQUIRED);
		}
		
		$kuserId = GamePlugin::getKuserIdFromPuserId($userId);
		
		// Redis returns 'false' if the provided userId does not exist
		$userRank = $redisWrapper->doZrevrank($redisKey, $kuserId);
		$userScore = $redisWrapper->doZscore($redisKey, $kuserId);
		if ($userScore === false || $userRank === false)
		{
			KalturaLog::info("No result found for userId {$this->userIdEqual} with key $redisKey");
			return array();
		}
		
		$result = array(array('rank' => $userRank, 'userId' => $userId, 'score' => $userScore));
		
		$redisWrapper->doZrem($redisKey, $kuserId);
		
		$response = new KalturaUserScorePropertiesResponse();
		$response->totalCount = count($result);
		$response->objects = KalturaUserScorePropertiesArray::fromDbArray($result, null);
		
		return $response;
	}
}