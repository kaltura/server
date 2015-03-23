<?php
/**
 * @package plugins.drm
 * @subpackage api.filters
 */
class KalturaDrmDeviceFilter extends KalturaDrmDeviceBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DrmDeviceFilter();
	}
}
