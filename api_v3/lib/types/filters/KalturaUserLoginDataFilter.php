<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserLoginDataFilter extends KalturaUserLoginDataBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new UserLoginDataFilter();
	}
}
