<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaAnswerCuePointFilter extends KalturaAnswerCuePointBaseFilter
{
    /* (non-PHPdoc)
     * @see KalturaCuePointFilter::getCriteria()
     */
    protected function getCriteria()
    {
        return KalturaCriteria::create('AnswerCuePoint');
    }
    
	/* (non-PHPdoc)
	 * @see KalturaCuePointFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return parent::getTypeListResponse($pager, $responseProfile, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_ANSWER));
	}
}
