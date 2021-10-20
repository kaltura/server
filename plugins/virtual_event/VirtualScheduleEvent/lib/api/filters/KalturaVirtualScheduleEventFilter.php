<?php
/**
 * @package 
 * @subpackage api.filters
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
