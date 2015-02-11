<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveChannelSegmentFilter extends KalturaLiveChannelSegmentBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new LiveChannelSegmentFilter();
	}
}
