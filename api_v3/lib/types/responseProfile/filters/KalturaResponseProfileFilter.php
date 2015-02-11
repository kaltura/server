<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaResponseProfileFilter extends KalturaResponseProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ResponseProfileFilter();
	}
}
