<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters
 */
class KalturaGenericDistributionProviderActionFilter extends KalturaGenericDistributionProviderActionBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new GenericDistributionProviderActionFilter();
	}
}
