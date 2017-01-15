<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaSimulatedLiveEntryScheduleEventFilter extends KalturaSimulatedLiveEntryScheduleEventBaseFilter
{
	/* (non-PHPdoc)
    * @see KalturaScheduleEventFilter::getListResponseType()
    */
	protected function getListResponseType()
	{
		return ScheduleEventType::SIMU_LIVE;
	}
}
