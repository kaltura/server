<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaLocationScheduleResourceFilter extends KalturaLocationScheduleResourceBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaScheduleResourceFilter::getListResponseType()
	 */
	protected function getListResponseType()
	{
		return ScheduleResourceType::LOCATION;
	}
}
