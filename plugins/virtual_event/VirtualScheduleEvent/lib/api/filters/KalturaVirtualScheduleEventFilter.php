<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters
 * @abstract
 * @relatedService ScheduleEventService
 */
class KalturaVirtualScheduleEventFilter extends KalturaVirtualScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	* @see KalturaScheduleEventFilter::getListResponseType()
	*/
	protected function getListResponseType()
	{
		return VirtualScheduleEventType::VIRTUAL;
	}
}
