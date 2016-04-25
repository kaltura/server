<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaRecordScheduleEventFilter extends KalturaRecordScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::RECORD;
	}
}
