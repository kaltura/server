<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserRoleFilter extends KalturaUserRoleBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new UserRoleFilter();
	}
}
