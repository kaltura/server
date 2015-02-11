<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters
 */
class KalturaDistributionProfileFilter extends KalturaDistributionProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DistributionProfileFilter();
	}
}
