<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.filters
 */
class KalturaEntryDistributionFilter extends KalturaEntryDistributionBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new EntryDistributionFilter();
	}
}
