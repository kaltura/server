<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.filters
 */
class KalturaEventNotificationTemplateFilter extends KalturaEventNotificationTemplateBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new EventNotificationTemplateFilter();
	}
}
