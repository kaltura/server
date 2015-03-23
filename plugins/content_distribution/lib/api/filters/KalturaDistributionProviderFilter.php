<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters
 */
class KalturaDistributionProviderFilter extends KalturaDistributionProviderBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		throw new Exception("Distribution providers can't be filtered");
	}
}
