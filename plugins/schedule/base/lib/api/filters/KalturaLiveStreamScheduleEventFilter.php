<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaLiveStreamScheduleEventFilter extends KalturaLiveStreamScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::LIVE_STREAM;
	}
}
