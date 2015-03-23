<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBaseSyndicationFeedFilter extends KalturaBaseSyndicationFeedBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new syndicationFeedFilter();
	}
}
