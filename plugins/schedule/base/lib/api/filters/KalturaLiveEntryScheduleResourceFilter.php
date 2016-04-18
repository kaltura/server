<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaLiveEntryScheduleResourceFilter extends KalturaLiveEntryScheduleResourceBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleResourceFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleResourceType::LIVE_ENTRY;
	}
}
