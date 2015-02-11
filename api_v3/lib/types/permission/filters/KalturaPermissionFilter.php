<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionFilter extends KalturaPermissionBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new PermissionFilter();
	}
}
