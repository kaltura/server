<?php
/**
 * @package api
 * @subpackage filters
 * @deprecated use KalturaAccessControlProfileFilter instead
 */
class KalturaAccessControlFilter extends KalturaAccessControlBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new accessControlFilter();
	}
}
