<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPermissionItemFilter extends KalturaPermissionItemBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new PermissionItemFilter();
	}
}
