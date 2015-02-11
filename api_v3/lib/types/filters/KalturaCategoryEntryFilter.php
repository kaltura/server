<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryEntryFilter extends KalturaCategoryEntryBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new categoryEntryFilter();
	}
}
