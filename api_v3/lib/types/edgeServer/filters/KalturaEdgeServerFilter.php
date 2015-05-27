<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaEdgeServerFilter extends KalturaEdgeServerBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new EdgeServerFilter();
	}
}
