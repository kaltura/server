<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaBlackoutScheduleEventFilter extends KalturaRecordScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::BLACKOUT;
	}
}
