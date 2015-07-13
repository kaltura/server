<?php
/**
 * @package plugins.quiz
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
}
