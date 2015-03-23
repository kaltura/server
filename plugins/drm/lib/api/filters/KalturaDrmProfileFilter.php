<?php
/**
 * @package plugins.drm
 * @subpackage api.filters
 */
class KalturaDrmProfileFilter extends KalturaDrmProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DrmProfileFilter();
	}
}
