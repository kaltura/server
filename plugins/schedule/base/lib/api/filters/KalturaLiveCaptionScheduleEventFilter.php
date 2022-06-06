<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaLiveCaptionScheduleEventFilter extends KalturaEntryScheduleEventFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleResourceFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::LIVE_CAPTION;
	}
}