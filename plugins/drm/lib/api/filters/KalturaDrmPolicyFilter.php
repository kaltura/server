<?php
/**
 * @package plugins.drm
 * @subpackage api.filters
 */
class KalturaDrmPolicyFilter extends KalturaDrmPolicyBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DrmPolicyFilter();
	}
}
