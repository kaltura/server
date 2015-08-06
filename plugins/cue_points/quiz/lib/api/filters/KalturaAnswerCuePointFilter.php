<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaAnswerCuePointFilter extends KalturaAnswerCuePointBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return parent::getTypeListResponse($pager, $responseProfile, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_ANSWER));
	}
}
