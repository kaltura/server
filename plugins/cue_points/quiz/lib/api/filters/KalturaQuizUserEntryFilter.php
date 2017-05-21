<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaQuizUserEntryFilter extends KalturaQuizUserEntryBaseFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = QuizPlugin::getApiValue(QuizUserEntryType::QUIZ);
		
		UserEntryPeer::setUseCriteriaFilter(false);
		$response = parent::getListResponse($pager, $responseProfile);
		UserEntryPeer::setUseCriteriaFilter(true);
		
		return $response;
	}
}
