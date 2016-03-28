<?php
/**
 * @package plugins.ask
 * @subpackage api.filters
 */
class KalturaQuestionCuePointFilter extends KalturaQuestionCuePointBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::validateForResponseProfile()
	 */
	public function validateForResponseProfile()
	{
		// override KalturaCuePointFilter::validateForResponseProfile because all question cue-points are public
	}

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return parent::getTypeListResponse($pager, $responseProfile, AskPlugin::getCoreValue('CuePointType',AskCuePointType::ASK_QUESTION));
	}
}
