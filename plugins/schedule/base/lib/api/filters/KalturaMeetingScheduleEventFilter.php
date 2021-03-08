<?php

/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaMeetingScheduleEventFilter extends KalturaMeetingScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::MEETING;
	}
}
