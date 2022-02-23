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
	 * @param int $score
	 * @param KalturaUserScorePropertiesFilter|null $filter
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function updateAction($score, KalturaUserScorePropertiesFilter $filter)
	{
		if (!$filter)
		{
			throw new KalturaAPIException(KalturaErrors::USER_SCORE_PROPERTIES_FILTER_REQUIRED);
		}
		
		return $filter->updateUserScore($score, $filter);
	}
}