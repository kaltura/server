<?php

/**
 * @service userScore
 * @package plugins.leaderboard
 * @subpackage api.services
 */
class UserScoreService extends KalturaBaseService
{
	/**
	 * @action list
	 * @param KalturaUserScorePropertiesFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function listAction(KalturaUserScorePropertiesFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaUserScorePropertiesFilter();
		}
		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		
		return $filter->getListResponse($pager);
	}
	
	/**
	 * @action update
	 * @param int $score
	 * @param KalturaUserScorePropertiesFilter|null $filter
	 * @return KalturaUserScorePropertiesResponse
	 */
	public function updateAction(int $score, KalturaUserScorePropertiesFilter $filter)
	{
		if (!$filter)
		{
			throw new KalturaAPIException(KalturaErrors::USER_SCORE_PROPERTIES_FILTER_REQUIRED);
		}
		
		return $filter->updateUserScore($score, $filter);
	}
	
	/**
	 * @action importFromCsv
	 * @param file $fileData
	 */
	public function importFromCsvAction($fileData)
	{
	
	}
}