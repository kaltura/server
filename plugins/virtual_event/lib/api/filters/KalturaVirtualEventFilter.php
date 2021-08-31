<?php
/**
 * @package plugins.virtualEvent
 * @subpackage api.filters
 */
class KalturaVirtualEventFilter extends KalturaScheduledTaskProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ScheduledTaskProfileFilter();
	}
}