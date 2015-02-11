<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAccessControlProfileFilter extends KalturaAccessControlProfileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new accessControlFilter();
	}
}
