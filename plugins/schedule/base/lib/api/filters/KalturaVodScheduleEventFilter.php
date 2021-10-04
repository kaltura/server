<?php

/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaVodScheduleEventFilter extends KalturaVodScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleEventFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleEventType::VOD;
	}
}