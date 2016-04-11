<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaCameraScheduleResourceFilter extends KalturaCameraScheduleResourceBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleResourceFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleResourceType::CAMERA;
	}
}
