<?php
/**
 * @package plugins.scheduledTask
 * @subpackage api.filters
 */
class KalturaScheduledTaskProfileFilter extends KalturaScheduledTaskProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ScheduledTaskProfileFilter();
	}
}
