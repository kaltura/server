<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaWidgetFilter extends KalturaWidgetBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new widgetFilter();
	}
}
