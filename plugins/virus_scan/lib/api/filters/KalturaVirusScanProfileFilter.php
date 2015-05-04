<?php
/**
 * @package plugins.virusScan
 * @subpackage api.filters
 */
class KalturaVirusScanProfileFilter extends KalturaVirusScanProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new VirusScanProfileFilter();
	}
}
