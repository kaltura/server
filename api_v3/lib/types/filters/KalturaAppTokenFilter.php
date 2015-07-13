<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAppTokenFilter extends KalturaAppTokenBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new appTokenFilter();
	}
}
